<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;


function renderTransactionList(int $accountId): string
{
    /** @var string[][] $tableDataRows Array of table data rows. */
    $tableDataRows = [];

    /** @var int[]|string[] $totalDataRow Table data total row. Int while being processed, converted to string for rendering. */
    $totalDataRow = [
        'expected' => 0,
    ];
    foreach (TransactionStatus::cases() as $transactionStatus) {
        $totalDataRow[$transactionStatus->toKey()] = 0;
    }


    // Fetch and compile data
    $entityManager = EntityManagerFactory::getEntityManager();
    $account = $entityManager->getRepository(Account::class)->find($accountId);
    foreach ($account->getTransactions() as $transaction) {
        $dataRow = [
            'status-class' => $transaction->getStatus()->toBootstrapColor(
            )->value,
            'date' => $transaction->getDate()->format('Y-m-d'),
            'destination' => $transaction->getDestination(),
            'desc' => $transaction->getDesc(),
            'expected' => Common::numberToAccounting($transaction->getAmount()),
        ];
        $totalDataRow['expected'] += $transaction->getAmount();
        foreach (TransactionStatus::cases() as $transactionStatus) {
            if ($transactionStatus == $transaction->getStatus()) {
                $dataRow[$transactionStatus->toKey()]
                    = Common::numberToAccounting(
                    $transaction->getAmount()
                );
                $totalDataRow[$transactionStatus->toKey()]
                    += $transaction->getAmount();
            } else {
                $dataRow[$transactionStatus->toKey()]
                    = Common::numberToAccounting(0);
            }
        }
        $tableDataRows[] = $dataRow;
    }
    foreach ($account->getTransfers() as $transfer) {
        $dataRow = [
            'status-class' => $transfer->getStatus()->toBootstrapColor()->value,
            'date' => $transfer->getDate()->format('Y-m-d'),
            'destination' => $transfer->getDestination(),
            'desc' => $transfer->getDesc(),
            'expected' => Common::numberToAccounting(-$transfer->getAmount()),
        ];
        $totalDataRow['expected'] -= $transfer->getAmount();
        foreach (TransactionStatus::cases() as $transactionStatus) {
            if ($transactionStatus == $transfer->getStatus()) {
                $dataRow[$transactionStatus->toKey()]
                    = Common::numberToAccounting(
                    -$transfer->getAmount()
                );
                $totalDataRow[$transactionStatus->toKey()]
                    -= $transfer->getAmount();
            } else {
                $dataRow[$transactionStatus->toKey()]
                    = Common::numberToAccounting(0);
            }
        }
        $tableDataRows[] = $dataRow;
    }
    foreach ($totalDataRow as $key => $value) {
        $totalDataRow[$key] = Common::numberToAccounting($value);
    }


    // Default data sorting - date, destination
    usort($tableDataRows, function (array $a, array $b) {
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
        <h2>Transactions for {$account->getName()}</h2>
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
    foreach ($tableDataRows as $tableDataRow) {
        $finalBody .= "
            <tr class='table-{$tableDataRow['status-class']}'>
                <td>{$tableDataRow["date"]}</td>
                <td>{$tableDataRow["destination"]}</td>
                <td>{$tableDataRow["desc"]}</td>
                <td>{$tableDataRow["planned"]}</td>
                <td>{$tableDataRow["pending"]}</td>
                <td>{$tableDataRow["processed"]}</td>
                <td>{$tableDataRow["expected"]}</td>
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
                    <th>{$totalDataRow['planned']}</th>
                    <th>{$totalDataRow['pending']}</th>
                    <th>{$totalDataRow['processed']}</th>
                    <th>{$totalDataRow['expected']}</th>
                </tr>
            </tfoot>
        </table>
    ";
    return $finalBody;
}
