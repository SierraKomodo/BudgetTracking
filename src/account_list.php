<?php

declare(strict_types = 1);

namespace SierraKomodo\BudgetTracking;


use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\DatabaseConnectionFactory;

require_once(__DIR__ . '/common.php');


function renderAccountList(): string
{
    // Fetch and compile data
    $conn = DatabaseConnectionFactory::getConnection();
    $accounts = $conn->fetchAllAssociative("SELECT * FROM `accounts`;") ?: [];
    foreach ($accounts as $key => $account) {
        $account['balance']  = 0;
        $account['expected'] = 0;
        $transactions        = $conn->fetchAllAssociative(
            "
                SELECT *
                FROM `transactions`
                WHERE `account` = :account;
            ", [
                'account' => $account['id'],
            ]
        ) ?: [];
        foreach ($transactions as $transaction) {
            $transaction['status'] = TransactionStatus::from($transaction['status']);
            if ($transaction['status'] == TransactionStatus::Processed) {
                $account['balance'] += $transaction['amount'];
            }
            $account['expected'] += $transaction['amount'];
        }
        $account['balance']  = numberToAccounting($account['balance']);
        $account['expected'] = numberToAccounting($account['expected']);
        $accounts[$key]      = $account;
    }
    
    
    // Render HTML
    $finalBody = "
        <h2>Accounts List</h2>
        <table class='table table-striped table-hover'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Account Type</th>
                    <th>Balance</th>
                    <th>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($accounts as $account) {
        $finalBody .= "
            <tr>
                <th>{$account['name']}</th>
                <td>{$account['desc']}</td>
                <td>{$account['account_type']}</td>
                <td>{$account['balance']}</td>
                <td>{$account['expected']}</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
        </table>
    ";
    
    
    return $finalBody;
}
