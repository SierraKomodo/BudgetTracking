<?php


namespace SierraKomodo\BudgetTracking;


// Required files
use SierraKomodo\BudgetTracking\Enum\AccountType;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\DatabaseConnectionFactory;

require_once(__DIR__ . '/common.php');


/**
 * @return string
 */
function renderSummary(): string
{
    // Common function vars
    $summaryCash             = 0;
    $summaryReserved         = 0;
    $summaryCredit           = 0;
    $cashAccounts            = [];
    $creditAccounts          = [];
    $otherAccounts           = [];
    $accountTypeStatusTotals = [];
    foreach (AccountType::cases() as $accountType) {
        foreach (TransactionStatus::cases() as $transactionStatus) {
            $accountTypeStatusTotals["{$accountType->toKey()}_{$transactionStatus->toKey()}"] = 0;
        }
        $accountTypeStatusTotals["{$accountType->toKey()}_expected"] = 0;
        $accountTypeStatusTotals["{$accountType->toKey()}_reserved"] = 0;
    }
    $accountTypeStatusTotals["credit_limit"]     = 0;
    $accountTypeStatusTotals["credit_usage"]     = 0.00;
    $accountTypeStatusTotals["credit_available"] = 0.00;
    $accountTypeStatusTotals["credit_rewards"]   = 0.00;
    $accountTypeStatusTotals["credit_payments"]  = 0;
    
    // Fetch and compile data
    $conn = DatabaseConnectionFactory::getConnection();
    $accounts = $conn->fetchAllAssociative(
        "
        SELECT *
        FROM `accounts`;
    "
    );
    foreach ($accounts as $account) {
        $account["account_type"]    = AccountType::from($account["account_type"]);
        $account["transactions"]    = $conn->fetchAllAssociative(
            "
                SELECT *
                FROM `transactions`
                WHERE `account` = :account;
            ", [
                "account" => $account["id"],
            ]
        );
        $account["transfers"]       = $conn->fetchAllAssociative(
            "
                SELECT *
                FROM `transactions`
                WHERE `dest_account` = :dest_account;
            ", [
                "dest_account" => $account["id"],
            ]
        );
        $account["reserves"]        = $conn->fetchAllAssociative(
            "
                SELECT *
                FROM `reserves`
                WHERE `account` = :account;
            ", [
                "account" => $account["id"],
            ]
        );
        $account["total_planned"]   = 0;
        $account["total_pending"]   = 0;
        $account["total_processed"] = 0;
        $account["total_expected"]  = 0;
        $account["total_reserved"]  = 0;
        $account["credit"]          = null;
        foreach ($account["transactions"] as $transaction) {
            $transaction["status"]                                                                            = TransactionStatus::from(
                $transaction["status"]
            );
            $account["total_{$transaction["status"]->toKey()}"]                                               += $transaction["amount"];
            $account["total_expected"]                                                                        += $transaction["amount"];
            $accountTypeStatusTotals["{$account["account_type"]->toKey()}_{$transaction["status"]->toKey()}"] += $transaction["amount"];
            $accountTypeStatusTotals["{$account["account_type"]->toKey()}_expected"]                          += $transaction["amount"];
        }
        foreach ($account["transfers"] as $transfer) {
            $transfer["amount"]                                                                            = -$transfer["amount"];
            $transfer["status"]                                                                            = TransactionStatus::from(
                $transfer["status"]
            );
            $account["total_{$transfer["status"]->toKey()}"]                                               += $transfer["amount"];
            $account["total_expected"]                                                                     += $transfer["amount"];
            $accountTypeStatusTotals["{$account["account_type"]->toKey()}_{$transfer["status"]->toKey()}"] += $transfer["amount"];
            $accountTypeStatusTotals["{$account["account_type"]->toKey()}_expected"]                       += $transfer["amount"];
        }
        foreach ($account["reserves"] as $reserve) {
            $summaryReserved                                                         += $reserve["amount"];
            $account["total_reserved"]                                               += $reserve["amount"];
            $account["total_expected"]                                               += $reserve["amount"];
            $accountTypeStatusTotals["{$account["account_type"]->toKey()}_reserved"] += $reserve["amount"];
            $accountTypeStatusTotals["{$account["account_type"]->toKey()}_expected"] += $reserve["amount"];
        }
        
        switch ($account["account_type"]) {
            case AccountType::Cash:
                $summaryCash    += $account["total_expected"];
                $cashAccounts[] = $account;
                break;
            case AccountType::Credit:
                $summaryCredit                               += $account["total_expected"];
                $account["credit"]                           = $conn->fetchAssociative(
                    "
                        SELECT *
                        FROM `accounts_credit`
                        WHERE `id` = :id;
                    ", [
                        "id" => $account["id"],
                    ]
                );
                $account["credit"]["available"]              = $account["credit"]["limit"] + $account["total_processed"];
                $account["credit"]["expected_available"]     = $account["credit"]["limit"] + $account["total_expected"];
                $account["credit"]["usage"]                  = round(
                    abs($account["total_processed"]) / $account["credit"]["limit"] * 100,
                    2
                );
                $account["credit"]["expected_usage"]         = round(
                    abs($account["total_expected"]) / $account["credit"]["limit"] * 100,
                    2
                );
                $account["credit"]["payments"]               = $account["credit"]["minimum_payment"] != 0 ? ceil(
                    abs($account["total_expected"]) / $account["credit"]["minimum_payment"]
                ) : null;
                $accountTypeStatusTotals["credit_limit"]     += $account["credit"]["limit"];
                $accountTypeStatusTotals["credit_available"] += $account["credit"]["expected_available"];
                $accountTypeStatusTotals["credit_rewards"]   += $account["credit"]["rewards"];
                $accountTypeStatusTotals["credit_payments"]  = max(
                    $accountTypeStatusTotals["credit_payments"],
                    $account["credit"]["payments"]
                );
                $creditAccounts[]                            = $account;
                break;
            case AccountType::Other:
                $otherAccounts[] = $account;
                break;
        }
    }
    $summaryTotal = $summaryCash + $summaryCredit + $summaryReserved;
    if ($accountTypeStatusTotals["credit_limit"]) {
        $accountTypeStatusTotals["credit_usage"] = round(
            abs($accountTypeStatusTotals["credit_expected"]) / $accountTypeStatusTotals["credit_limit"] * 100,
            2
        );
    }
    
    /* Cash Available */
    $availableTable = "
        <h2>Overview</h2>
        <table class='table table-sm table-striped table-hover'>
            <tbody>
                <tr>
                    <th>Cash In Hand</th>
                    <td>" . numberToAccounting($summaryCash) . "</td>
                </tr>
                <tr>
                    <th>Reserved</th>
                    <td>" . numberToAccounting($summaryReserved) . "</td>
                </tr>
                <tr>
                    <th>Credit Owed</th>
                    <td>" . numberToAccounting($summaryCredit) . "</td>
                </tr>
                <tr>
                    <th>True Balance</th>
                    <td>" . numberToAccounting($summaryTotal) . "</td>
                </tr>
            </tbody>
        </table>
    ";
    
    
    /* Cash Accounts */
    $cashAccountsTable = "
        <h2>Cash Accounts</h2>
        <table class='table table-sm table-striped table-hover'>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Planned</th>
                    <th>Pending</th>
                    <th>Processed</th>
                    <th>Reserved</th>
                    <th>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($cashAccounts as $cashAccount) {
        $cashAccountsTable .= "
                <tr>
                    <th>{$cashAccount["name"]}</th>
                    <td>" . numberToAccounting($cashAccount["total_planned"]) . "</td>
                    <td>" . numberToAccounting($cashAccount["total_pending"]) . "</td>
                    <td>" . numberToAccounting($cashAccount["total_processed"]) . "</td>
                    <td>" . numberToAccounting($cashAccount["total_reserved"]) . "</td>
                    <td>" . numberToAccounting($cashAccount["total_expected"]) . "</td>
                </tr>
        ";
    }
    $cashAccountsTable .= "
            </tbody>
            <tfoot>
                <tr>
                    <th scope='row'>TOTAL</th>
                    <td>" . numberToAccounting($accountTypeStatusTotals["cash_planned"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["cash_pending"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["cash_processed"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["cash_reserved"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["cash_expected"]) . "</td>
                </tr>
            </tfoot>
        </table>
    ";
    
    
    /* Credit Accounts */
    $creditAccountsTable = "
        <h2>Credit Accounts</h2>
        <table class='table table-sm table-striped table-hover'>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Planned</th>
                    <th>Pending</th>
                    <th>Processed</th>
                    <th>Reserved</th>
                    <th>Expected</th>
                    <th>Usage</th>
                    <th>Available</th>
                    <th>Rewards</th>
                    <th>Payments</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($creditAccounts as $creditAccount) {
        $creditAccountsTable .= "
                <tr>
                    <th>{$creditAccount["name"]}</th>
                    <td>" . numberToAccounting($creditAccount["total_planned"]) . "</td>
                    <td>" . numberToAccounting($creditAccount["total_pending"]) . "</td>
                    <td>" . numberToAccounting($creditAccount["total_processed"]) . "</td>
                    <td>" . numberToAccounting($creditAccount["total_reserved"]) . "</td>
                    <td>" . numberToAccounting($creditAccount["total_expected"]) . "</td>
                    <td>" . numberToPercent($creditAccount["credit"]["expected_usage"]) . "</td>
                    <td>" . numberToAccounting($creditAccount["credit"]["expected_available"]) . "</td>
                    <td>" . numberToAccounting($creditAccount["credit"]["rewards"]) . "</td>
                    <td>" . $creditAccount["credit"]["payments"] . "</td>
                </tr>
        ";
    }
    $creditAccountsTable .= "
            </tobdy>
            <tfoot>
                <tr>
                    <th scope='row'>TOTAL</th>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_planned"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_pending"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_processed"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_reserved"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_expected"]) . "</td>
                    <td>" . numberToPercent($accountTypeStatusTotals["credit_usage"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_available"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["credit_rewards"]) . "</td>
                    <td>" . $accountTypeStatusTotals["credit_payments"] . "</td>
                </tr>
            </tfoot>
        </table>
    ";
    
    
    /* Other Accounts */
    $otherAccountsTable = "
        <h2>Other Accounts</h2>
        <table class='table table-sm table-striped table-hover'>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Planned</th>
                    <th>Pending</th>
                    <th>Processed</th>
                    <th>Reserved</th>
                    <th>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($otherAccounts as $otherAccount) {
        $otherAccountsTable .= "
                <tr>
                    <th scope='row'>{$otherAccount["name"]}</th>
                    <td>" . numberToAccounting($otherAccount["total_planned"]) . "</td>
                    <td>" . numberToAccounting($otherAccount["total_pending"]) . "</td>
                    <td>" . numberToAccounting($otherAccount["total_processed"]) . "</td>
                    <td>" . numberToAccounting($otherAccount["total_reserved"]) . "</td>
                    <td>" . numberToAccounting($otherAccount["total_expected"]) . "</td>
                </tr>
        ";
    }
    $otherAccountsTable .= "
            </tbody>
            <tfoot>
                <tr>
                    <th scope='row'>TOTAL</th>
                    <td>" . numberToAccounting($accountTypeStatusTotals["other_planned"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["other_pending"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["other_processed"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["other_reserved"]) . "</td>
                    <td>" . numberToAccounting($accountTypeStatusTotals["other_expected"]) . "</td>
                </tr>
            </tfoot>
        </table>
    ";
    
    
    /* Merge tables */
    return "<h1>Summary</h1>" . implode(
            "<hr />",
            [$availableTable, $cashAccountsTable, $creditAccountsTable, $otherAccountsTable]
        );
}
