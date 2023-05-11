<?php


namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Enum\TransactionStatus;

use function usort;

require_once('database.php');
require_once('common.php');


function renderTransactionList(int $accountId): string
{
    global $conn;


    // Common vars
    $amountTotal = 0;


    // Fetch and compile data
    $account = $conn->fetchAssociative(
        "
            SELECT `name`
            FROM `accounts`
            WHERE `id` = :id;
        ",
        [
            "id" => $accountId,
        ]
    );

    $transactions = $conn->fetchAllAssociative(
        "
            SELECT *
            FROM `transactions`
            WHERE `account` = :account;
        ",
        [
            "account" => $accountId,
        ]
    );

    $transfers = $conn->fetchAllAssociative(
        "
            SELECT *
            FROM `transactions`
            WHERE `dest_account` = :dest_account;
        ",
        [
            "dest_account" => $accountId,
        ]
    );

    foreach ($transfers as $transfer) {
        $transfer["amount"] = -$transfer["amount"];
        $transactions[] = $transfer;
    }

    foreach ($transactions as $transaction) {
        $transaction["status"] = TransactionStatus::from($transaction["status"]);
        $amountTotal += $transaction["amount"];
    }

    // Default Sort: Date, Destination
    usort($transactions, function (array $a, array $b) {
        if ($a['date'] < $b['date']) {
            return -1;
        }
        if ($a['date'] > $b['date']) {
            return 1;
        }
        if ($a['destination'] < $b['destination']) {
            return -1;
        }
        if ($a['destination'] > $b['destination']) {
            return 1;
        }
        return 0;
    });


    // Render HTML
    $finalBody = "
        <h2>Transactions for {$account["name"]}</h2>
        <table class='table table-sm table-striped table-hover'>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Destination</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
        ";
    foreach ($transactions as $transaction) {
        $finalBody .= "
            <tr>
                <td>{$transaction["date"]}</td>
                <td>{$transaction["destination"]}</td>
                <td>{$transaction["desc"]}</td>
                <td>{$transaction["status"]}</td>
                <td>" . numberToAccounting($transaction["amount"]) . "</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>" . numberToAccounting($amountTotal) . "</th>
                </tr>
            </tfoot>
        </table>
    ";
    return $finalBody;
}
