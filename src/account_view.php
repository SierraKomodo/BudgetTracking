<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;

use function usort;

function renderAccountView(int $accountId): string
{
    $alert = new Alert('View Account', 'No action result.');
    $alert->setColor(BootstrapColor::Danger);

    /** @var string[][] $transactionsDataRows List of Transaction table data rows. */
    $transactionsDataRows = [];
    $transactionsDataTotals = [
        'amount' => 0,
    ];

    /** @var float[]|string[] $accountSummaryTotals Map of totals for each TransactionStatus from the list of all Transactions. */
    $accountSummaryTotals = [
        'expected' => 0,
        'balance' => 0,
    ];
    foreach (TransactionStatus::cases() as $transactionStatus) {
        $accountSummaryTotals[$transactionStatus->toKey()] = 0;
    }


    // Fetch and compile data
    $entityManager = EntityManagerFactory::getEntityManager();
    $account = $entityManager->getRepository(Account::class)->find($accountId);
    if (empty($account)) {
        $alert->setContent('Failed to fetch account.');
        return $alert->render();
    }

    // Transactions List Table
    foreach ($account->getTransactions() as $transaction) {
        $transactionStatus = $transaction->getStatus();
        $amount = $transaction->getAmount();
        $dataRow = [
            'status-class' => $transactionStatus->toBootstrapColor()->value,
            'date' => $transaction->getDate()->format('Y-m-d'),
            'destination' => $transaction->getDestination(),
            'status' => $transaction->getStatus()->value,
            'amount' => Common::numberToAccounting($amount),
        ];
        $transactionsDataTotals['amount'] += $amount;
        $accountSummaryTotals[$transactionStatus->toKey()] += $amount;
        $accountSummaryTotals['expected'] += $amount;
        if ($transactionStatus == TransactionStatus::Processed) {
            $accountSummaryTotals['balance'] += $amount;
        }
        $transactionsDataRows[] = $dataRow;
    }
    foreach ($account->getTransfers() as $transfer) {
        $transactionStatus = $transfer->getStatus();
        $amount = $transfer->getAmount();
        $dataRow = [
            'status-class' => $transactionStatus->toBootstrapColor()->value,
            'date' => $transfer->getDate()->format('Y-m-d'),
            'destination' => $transfer->getDestination(),
            'status' => $transfer->getStatus()->value,
            'amount' => Common::numberToAccounting($amount),
        ];
        $transactionsDataTotals['amount'] -= $amount;
        $accountSummaryTotals[$transactionStatus->toKey()] -= $amount;
        $accountSummaryTotals['expected'] -= $amount;
        if ($transactionStatus == TransactionStatus::Processed) {
            $accountSummaryTotals['balance'] -= $amount;
        }
        $transactionsDataRows[] = $dataRow;
    }
    foreach ($accountSummaryTotals as $key => $value) {
        $accountSummaryTotals[$key] = Common::numberToAccounting($value);
    }
    foreach ($transactionsDataTotals as $key => $value) {
        $transactionsDataTotals[$key] = Common::numberToAccounting($value);
    }
    usort($transactionsDataRows, function (array $a, array $b) {
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
        <h2>Account {$account->getName()} Summary</h2>
        <p>{$account->getDesc()}</p>
        <table class='table table-sm'>
            <thead>
                <tr>
        ";
    foreach ($accountSummaryTotals as $key => $value) {
        $finalBody .= "<th>{$key}</th>";
    }
    $finalBody .= "
                </tr>
            </thead>
            <tbody>
                <tr>
        ";
    foreach ($accountSummaryTotals as $key => $value) {
        $finalBody .= "<td>{$value}</td>";
    }
    $finalBody .= "
                </tr>
            </tbody>
        </table>
        
        <h2>Transaction History</h2>
        <table class='table table-sm table-hover'>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th>Expected</th>
                </tr>
            </thead>
            <tbody>
        ";
    foreach ($transactionsDataRows as $transactionsDataRow) {
        $finalBody .= "
            <tr class='table-{$transactionsDataRow['status-class']}'>
                <td>{$transactionsDataRow["date"]}</td>
                <td>{$transactionsDataRow["destination"]}</td>
                <td>{$transactionsDataRow["status"]}</td>
                <td>{$transactionsDataRow["amount"]}</td>
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
                    <th>{$transactionsDataTotals['amount']}</th>
                </tr>
            </tfoot>
        </table>
    ";
    return $finalBody;
}
