<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

use Doctrine\DBAL\Exception as DbalException;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Exception;
use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\AccountType;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;
use SierraKomodo\BudgetTracking\Model\AccountCredit;


$alert = new Alert('Add Account', 'No action result.');
$alert->setColor(BootstrapColor::Danger);


// Validate data
if ($_POST["account_type"] == "Credit" && !$_POST["limit"]) {
    $alert->setContent('Limit is a required field for credit accounts.');
    $_GET["page"] = "account/add";
    return $alert->render();
}


// Fetch and compile data
try {
    $entityManager = EntityManagerFactory::getEntityManager();
} catch (DbalException|MissingMappingDriverImplementation) {
    $alert->setContent('Failed to initialize the database connection.');
    $_GET['page'] = 'transaction/add';
    return $alert->render();
}

// Account type
$accountType = AccountType::from($_POST['account_type']);


// Insert account
$account = new Account();
$account->setName($_POST['name']);
if ($_POST['desc']) {
    $account->setDesc($_POST['desc']);
}
$account->setAccountType($accountType);
$entityManager->persist($account);

// Insert credit account
if ($accountType == AccountType::Credit) {
    $credit = new AccountCredit();
    $credit->setLimit($_POST['limit']);
    if ($_POST['minimum_payment']) {
        $credit->setMinimumPayment($_POST['minimum_payment']);
    }
    if ($_POST['rewards']) {
        $credit->setMinimumPayment($_POST['rewards']);
    }
    $account->setCredit($credit);
    $entityManager->persist($credit);
}

try {
    $entityManager->flush();
} catch (Exception $e) {
    $alert->setContent('Failed to write to the database.' . $e->getMessage());
    $_POST['page'] = 'transaction/add';
    return $alert->render();
}


// Result
$alert->setContent(
    "Successfully added account {$account->getName()}."
);
$alert->setColor(BootstrapColor::Success);
return $alert->render();
