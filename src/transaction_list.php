<?php


namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Enum\TransactionStatus;

use SierraKomodo\BudgetTracking\Factory\DatabaseConnectionFactory;

use function usort;

require_once('common.php');


function renderTransactionList(int $accountId): string
{
    // Common vars
    $plannedTotal   = 0;
    $pendingTotal   = 0;
    $processedTotal = 0;
    $amountTotal    = 0;
    
    
    // Fetch and compile data
    $conn = DatabaseConnectionFactory::getConnection();
    $account = $conn->fetchAssociative(
        "
            SELECT `name`
            FROM `accounts`
            WHERE `id` = :id;
        ", [
            "id" => $accountId,
        ]
    );
    
    $transactions = $conn->fetchAllAssociative(
        "
            SELECT *
            FROM `transactions`
            WHERE `account` = :account;
        ", [
            "account" => $accountId,
        ]
    );
    
    $transfers = $conn->fetchAllAssociative(
        "
            SELECT *
            FROM `transactions`
            WHERE `dest_account` = :dest_account;
        ", [
            "dest_account" => $accountId,
        ]
    );
    
    foreach ($transfers as $transfer) {
        $transfer["amount"] = -$transfer["amount"];
        $transactions[]     = $transfer;
    }
    
    foreach ($transactions as $key => $transaction) {
        $transaction["status"]    = TransactionStatus::from($transaction["status"]);
        $transaction['planned']   = $transaction['status'] == TransactionStatus::Planned ? $transaction['amount'] : 0;
        $transaction['pending']   = $transaction['status'] == TransactionStatus::Pending ? $transaction['amount'] : 0;
        $transaction['processed'] = $transaction['status'] == TransactionStatus::Processed ? $transaction['amount'] : 0;
        $plannedTotal             += $transaction['planned'];
        $pendingTotal             += $transaction['pending'];
        $processedTotal           += $transaction['processed'];
        $amountTotal              += $transaction["amount"];
        $transactions[$key]       = $transaction;
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
        <table class='table table-sm table-hover'>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Destination</th>
                    <th>Description</th>
                    <th>Planned</th>
                    <th>Pending</th>
                    <th>Processed</th>
                    <th>Expected</th>
                </tr>
            </thead>
            <tbody>
        ";
    foreach ($transactions as $transaction) {
        $finalBody .= "
            <tr class='table-{$transaction['status']->toBootstrapColor()->value}'>
                <td>{$transaction["date"]}</td>
                <td>{$transaction["destination"]}</td>
                <td>{$transaction["desc"]}</td>
                <td>" . numberToAccounting($transaction["planned"]) . "</td>
                <td>" . numberToAccounting($transaction["pending"]) . "</td>
                <td>" . numberToAccounting($transaction["processed"]) . "</td>
                <th>" . numberToAccounting($transaction["amount"]) . "</th>
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
                    <th>" . numberToAccounting($plannedTotal) . "</th>
                    <th>" . numberToAccounting($pendingTotal) . "</th>
                    <th>" . numberToAccounting($processedTotal) . "</th>
                    <th>" . numberToAccounting($amountTotal) . "</th>
                </tr>
            </tfoot>
        </table>
    ";
    return $finalBody;
}
