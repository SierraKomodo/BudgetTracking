<?php

namespace SierraKomodo\BudgetTracking;

use PDO;
use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputDate;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputNumber;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Options\OptionsSelect;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;

require_once('database.php');


function renderAddTransaction(): string
{
    global $database;

    // Fetch and compile data
    $accounts = $database->query("SELECT * FROM `accounts`;")->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $accountSelectGroup = [];
    foreach ($accounts as $account) {
        $accountSelectGroup[$account["id"]] = $account["name"];
    }

    // Form
    $form = new Form("transaction/add", "transaction/add", "Transaction");
    $form->addField(new InputDate("date", "Date", TRUE));
    $form->addField(new OptionsSelect("account", "Account", TRUE, $accountSelectGroup));
    $form->addField(new OptionsSelect("dest_account", "Destination Account", options: $accountSelectGroup));
    $form->addField(new InputText("destination", "Destination"));
    $form->addField(new InputText("desc", "Description"));
    $form->addField(new InputNumber("amount", "Amount", TRUE));
    $form->addField(TransactionStatus::toOptionsSelect("status", "Status", TRUE));
    return $form->render();
}