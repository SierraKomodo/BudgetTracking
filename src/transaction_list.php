<?php


namespace SierraKomodo\BudgetTracking;

use PDO;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;

require_once('database.php');
require_once('common.php');


function renderTransactionList(int $accountId): string
{
    global $database;


    // Database & Statements
    $accountStmt = $database->prepare("
        SELECT `name` FROM `accounts`
            WHERE `id` = :id;
    ");
    $transactionStmt = $database->prepare("
        SELECT * FROM `transactions`
            WHERE `account` = :account;
    ");
    $transferStmt = $database->prepare("
        SELECT * FROM `transactions`
            WHERE `dest_account` = :dest_account;
    ");


    // Common vars
    $amountTotal = 0;


    // Fetch and compile data
    $accountStmt->execute([":id" => $accountId]);
    $account = $accountStmt->fetch(PDO::FETCH_ASSOC);

    $transactionStmt->execute([":account" => $accountId]);
    $transactions = $transactionStmt->fetchAll(PDO::FETCH_ASSOC);

    $transferStmt->execute([":dest_account" => $accountId]);
    $transfers = $transferStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($transfers as $transfer) {
        $transfer["amount"] = -$transfer["amount"];
        $transactions[] = $transfer;
    }

    foreach($transactions as $transaction) {
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
