<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;


// Required files
use SierraKomodo\BudgetTracking\Enum\AccountType;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;


/**
 * @return string
 */
function renderSummary(): string
{
    /** @var string[][] $overviewTableDataRows */
    $overviewTableDataRows = [
        'cash' => 0,
        'reserved' => 0,
        'credit' => 0,
        'balance' => 0,
    ];

    /** @var string[][] $cashTableDataRows */
    $cashTableDataRows = [];
    $cashTotalDataRow = [
        'balance' => 0,
        'expected' => 0,
    ];

    /** @var string[][] $creditTableDataRows */
    $creditTableDataRows = [];
    $creditTotalDataRow = [
        'balance' => 0,
        'expected' => 0,
        'limit' => 0,
        'usage' => 0,
        'available' => 0,
        'rewards' => 0,
    ];

    /** @var string[][] $reserveTableDataRows */
    $reserveTableDataRows = [];
    $reserveTotalDataRow = [
        'balance' => 0,
        'expected' => 0,
    ];

    /** @var string[][] $otherTableDataRows */
    $otherTableDataRows = [];
    $otherTotalDataRow = [
        'balance' => 0,
        'expected' => 0,
    ];


    // Fetch and compile data
    $entityManager = EntityManagerFactory::getEntityManager();
    $accounts = $entityManager->getRepository(Account::class)->findAll();
    foreach ($accounts as $account) {
        $transactionTotal = $account->getAllTransactionTotal();
        $processedTotal = $account->getTransactionTotal(
            TransactionStatus::Processed
        );
        switch ($account->getAccountType()) {
            case AccountType::Cash:
                $overviewTableDataRows['cash'] += $transactionTotal;
                $overviewTableDataRows['balance'] += $transactionTotal;
                $cashTableDataRows[] = [
                    'name' => $account->getName(),
                    'balance' => Common::numberToAccounting($processedTotal),
                    'expected' => Common::numberToAccounting($transactionTotal),
                ];
                $cashTotalDataRow['balance'] += $processedTotal;
                $cashTotalDataRow['expected'] += $transactionTotal;
                break;

            case AccountType::Credit:
                $limit = $account->getCredit()->getLimit();
                $usagePercentage = -$transactionTotal;
                $usagePercentage /= $limit;
                $usagePercentage *= 100;
                $rewards = $account->getCredit()->getRewards();
                $overviewTableDataRows['credit'] += $transactionTotal;
                $overviewTableDataRows['balance'] += $transactionTotal;
                $creditTableDataRows[] = [
                    'name' => $account->getName(),
                    'balance' => Common::numberToAccounting($processedTotal),
                    'expected' => Common::numberToAccounting($transactionTotal),
                    'limit' => Common::numberToAccounting($limit),
                    'usage' => Common::numberToPercent($usagePercentage),
                    'available' => Common::numberToAccounting(
                        $limit + $transactionTotal
                    ),
                    'rewards' => Common::numberToAccounting($rewards),
                ];
                $creditTotalDataRow['balance'] += $processedTotal;
                $creditTotalDataRow['expected'] += $transactionTotal;
                $creditTotalDataRow['limit'] += $limit;
                $creditTotalDataRow['usage'] += $usagePercentage;
                $creditTotalDataRow['available'] += $limit + $transactionTotal;
                $creditTotalDataRow['rewards'] += $rewards;
                break;

            case AccountType::Reserve:
                $overviewTableDataRows['reserved'] += $transactionTotal;
                $overviewTableDataRows['balance'] -= $transactionTotal;
                $reserveTableDataRows[] = [
                    'name' => $account->getName(),
                    'balance' => Common::numberToAccounting($processedTotal),
                    'expected' => Common::numberToAccounting($transactionTotal),
                ];
                $reserveTotalDataRow['balance'] += $processedTotal;
                $reserveTotalDataRow['expected'] += $transactionTotal;
                break;

            case AccountType::Other:
                // Not included in the overview.
                $otherTableDataRows[] = [
                    'name' => $account->getName(),
                    'balance' => Common::numberToAccounting($processedTotal),
                    'expected' => Common::numberToAccounting($transactionTotal),
                ];
                $otherTotalDataRow['balance'] += $processedTotal;
                $otherTotalDataRow['expected'] += $transactionTotal;
                break;
        }
    }

    foreach ($overviewTableDataRows as $key => $value) {
        $overviewTableDataRows[$key] = Common::numberToAccounting($value);
    }
    foreach ($cashTotalDataRow as $key => $value) {
        $cashTotalDataRow[$key] = Common::numberToAccounting($value);
    }
    if (count($creditTableDataRows) != 0) {
        $creditTotalDataRow['usage'] /= count($creditTableDataRows);
    }
    foreach ($creditTotalDataRow as $key => $value) {
        if ($key == 'usage') {
            $creditTotalDataRow[$key] = Common::numberToPercent($value);
            continue;
        }
        $creditTotalDataRow[$key] = Common::numberToAccounting($value);
    }
    foreach ($reserveTotalDataRow as $key => $value) {
        $reserveTotalDataRow[$key] = Common::numberToAccounting($value);
    }
    foreach ($otherTotalDataRow as $key => $value) {
        $otherTotalDataRow[$key] = Common::numberToAccounting($value);
    }


    // Render HTML
    $finalBody = "
        <h2>Summary</h2>
        <table class='table table-sm table-striped table-hover'>
            <caption>Balance Summary</caption>
            <tbody>
                <tr>
                    <th scope='row'>Cash In Hand</th>
                    <td>{$overviewTableDataRows['cash']}</td>
                </tr>
                <tr>
                    <th scope='row'>Reserved</th>
                    <td>{$overviewTableDataRows['reserved']}</td>
                </tr>
                <tr>
                    <th scope='row'>Credit Owed</th>
                    <td>{$overviewTableDataRows['credit']}</td>
                </tr>
                <tr>
                    <th scope='row'>True Balance</th>
                    <td>{$overviewTableDataRows['balance']}</td>
                </tr>
            </tbody>
        </table>
        
        <table class='table table-sm table-striped table-hover'>
            <caption>Cash Accounts</caption>
            <thead>
                <tr>
                    <th scope='col'>Name</th>
                    <th scope='col'>Balance</th>
                    <th scope='col'>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($cashTableDataRows as $dataRow) {
        $finalBody .= "
            <tr>
                <th scope='row'>{$dataRow['name']}</th>
                <td>{$dataRow['balance']}</td>
                <td>{$dataRow['expected']}</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
            <tfoot>
                <tr>
                    <th scope='row'>Total</th>
                    <td>{$cashTotalDataRow['balance']}</td>
                    <td>{$cashTotalDataRow['expected']}</td>
                </tr>
            </tfoot>
        </table>
        
        
        <table class='table table-sm table-striped table-hover'>
            <caption>Credit Accounts</caption>
            <thead>
                <tr>
                    <th scope='col'>Name</th>
                    <th scope='col'>Balance</th>
                    <th scope='col'>Expected</th>
                    <th scope='col'>Limit</th>
                    <th scope='col'>Usage</th>
                    <th scope='col'>Available</th>
                    <th scope='col'>Rewards</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($creditTableDataRows as $dataRow) {
        $finalBody .= "
            <tr>
                <th scope='row'>{$dataRow['name']}</th>
                <td>{$dataRow['balance']}</td>
                <td>{$dataRow['expected']}</td>
                <td>{$dataRow['limit']}</td>
                <td>{$dataRow['usage']}</td>
                <td>{$dataRow['available']}</td>
                <td>{$dataRow['rewards']}</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
            <tfoot>
                <tr>
                    <th scope='row'>Total</th>
                    <td>{$creditTotalDataRow['balance']}</td>
                    <td>{$creditTotalDataRow['expected']}</td>
                    <td>{$creditTotalDataRow['limit']}</td>
                    <td>{$creditTotalDataRow['usage']}</td>
                    <td>{$creditTotalDataRow['available']}</td>
                    <td>{$creditTotalDataRow['rewards']}</td>
                </tr>
            </tfoot>
        </table>
        
        
        <table class='table table-sm table-striped table-hover'>
            <caption>Reserve Accounts</caption>
            <thead>
                <tr>
                    <th scope='col'>Name</th>
                    <th scope='col'>Balance</th>
                    <th scope='col'>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($reserveTableDataRows as $dataRow) {
        $finalBody .= "
            <tr>
                <th scope='row'>{$dataRow['name']}</th>
                <td>{$dataRow['balance']}</td>
                <td>{$dataRow['expected']}</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
            <tfoot>
                <tr>
                    <th scope='row'>Total</th>
                    <td>{$reserveTotalDataRow['balance']}</td>
                    <td>{$reserveTotalDataRow['expected']}</td>
                </tr>
            </tfoot>
        </table>
        
        
        <table class='table table-sm table-striped table-hover'>
            <caption>Other Accounts</caption>
            <thead>
                <tr>
                    <th scope='col'>Name</th>
                    <th scope='col'>Balance</th>
                    <th scope='col'>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($otherTableDataRows as $dataRow) {
        $finalBody .= "
            <tr>
                <th scope='row'>{$dataRow['name']}</th>
                <td>{$dataRow['balance']}</td>
                <td>{$dataRow['expected']}</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
            <tfoot>
                <tr>
                    <th scope='row'>Total</th>
                    <td>{$otherTotalDataRow['balance']}</td>
                    <td>{$otherTotalDataRow['expected']}</td>
                </tr>
            </tfoot>
        </table>
    ";

    return $finalBody;
}
