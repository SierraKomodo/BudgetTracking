<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;


use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;


function renderAccountList(): string
{
    /** @var string[][] $tableDataRows Array of table data rows. */
    $tableDataRows = [];

    /** @var int[]|string[] $totalDataRow Table data total row. Int while being processed, converted to string for rendering. */
    $totalDataRow = [
        'balance' => 0,
        'expected' => 0,
    ];


    // Fetch and compile data
    $entityManager = EntityManagerFactory::getEntityManager();
    $accounts = $entityManager->getRepository(Account::class)->findAll();
    foreach ($accounts as $account) {
        $balance = $account->getTransactionTotal(TransactionStatus::Processed);
        $expected = $account->getAllTransactionTotal();
        $tableDataRows[] = [
            'name' => $account->getName(),
            'type' => $account->getAccountType()->value,
            'balance' => Common::numberToAccounting($balance),
            'expected' => Common::numberToAccounting($expected),
        ];
        $totalDataRow['balance'] += $balance;
        $totalDataRow['expected'] += $expected;
    }
    $totalDataRow['balance'] = Common::numberToAccounting(
        $totalDataRow['balance']
    );
    $totalDataRow['expected'] = Common::numberToAccounting(
        $totalDataRow['expected']
    );


    // Default data sorting - name
    usort($tableDataRows, function (array $a, array $b) {
        if ($a['name'] < $b['name']) {
            return -1;
        }
        if ($a['name'] > $b['name']) {
            return 1;
        }
        return 0;
    });


    // Render HTML
    $finalBody = "
        <h2>Accounts List</h2>
        <table class='table table-striped table-hover'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Account Type</th>
                    <th>Balance</th>
                    <th>Expected</th>
                </tr>
            </thead>
            <tbody>
    ";
    foreach ($tableDataRows as $tableDataRow) {
        $finalBody .= "
            <tr>
                <th>{$tableDataRow['name']}</th>
                <td>{$tableDataRow['type']}</td>
                <td>{$tableDataRow['balance']}</td>
                <td>{$tableDataRow['expected']}</td>
            </tr>
        ";
    }
    $finalBody .= "
            </tbody>
            <tfoot>
                <tr>
                    <th>TOTAL</th>
                    <td>&nbsp;</td>
                    <td>{$totalDataRow['balance']}</td>
                    <td>{$totalDataRow['expected']}</td>
                </tr>
            </tfoot>
        </table>
    ";


    return $finalBody;
}
