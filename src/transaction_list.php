<?php


namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Enum\TransactionStatus;

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


    // Render HTML
    $finalBody = "
        <h2>Transactions for {$account["name"]}</h2>
        <table>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>" . numberToAccounting($amountTotal) . "</td>
                </tr>
            </tfoot>
        </table>
    ";
    return $finalBody;
}
