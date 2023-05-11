<?php

namespace SierraKomodo\BudgetTracking;

use DateTimeImmutable;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Exception;
use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;
use SierraKomodo\BudgetTracking\Model\Transaction;


require_once(__DIR__ . '/common.php');


$alert = new Alert('Add Transaction', 'No action result.');
$alert->setColor(BootstrapColor::Danger);


// Validate data
if (!$_POST["dest_account"] && !$_POST["destination"]) {
    $alert->setContent('Either destination or destination account is required.');
    $_GET["page"] = "transaction/add";
    return $alert->render();
}


// Fetch and compile data
try {
    $entityManager = EntityManagerFactory::getEntityManager();
} catch (DbalException|MissingMappingDriverImplementation) {
    $alert->setContent('Failed to initialize the database connection.');
    $_GET["page"] = "transaction/add";
    return $alert->render();
}

// Date
try {
    $date = new DateTimeImmutable($_POST['date']);
} catch (Exception) {
    $alert->setContent('Failed to initialize the datetime field. Your date may be invalid.');
    $_GET['page'] = 'transaction/add';
    return $alert->render();
}

// Account
$account = $entityManager->getRepository(Account::class)->find($_POST['account']);

// Destination Account
$destAccount = null;
if ($_POST["dest_account"]) {
    $destAccount = $entityManager->getRepository(Account::class)->find($_POST['dest_account']);
}

// Status
$status = TransactionStatus::from($_POST['status']);


// Insert transaction
$transaction = new Transaction();
$transaction->setDate($date);
$transaction->setAccount($account);
if ($destAccount) {
    $transaction->setDestAccount($destAccount);
}
if ($_POST['destination']) {
    $transaction->setDestination($_POST['destination']);
}
if ($_POST['desc']) {
    $transaction->setDesc($_POST['desc']);
}
$transaction->setAmount($_POST['amount']);
$transaction->setStatus($status);
$entityManager->persist($transaction);

try {
    $entityManager->flush();
} catch (Exception $e) {
    $alert->setContent('Failed to write to the database.' . $e->getMessage());
    $_GET['page'] = 'transaction/add';
    return $alert->render();
}


// Result
$alert->setContent(
    "Successfully added transaction amount " . numberToAccounting(
        $transaction->getAmount()
    ) . " to account {$account->getName()}."
);
$alert->setColor(BootstrapColor::Success);
return $alert->render();
