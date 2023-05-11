<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputDate;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputMoney;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Options\OptionsSelect;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\DatabaseConnectionFactory;


function renderAddTransaction(): string
{
    // Fetch and compile data
    $conn = DatabaseConnectionFactory::getConnection();
    $accounts = $conn->fetchAllAssociative(
        "
        SELECT *
        FROM `accounts`;
    "
    );
    $accountSelectGroup = [];
    foreach ($accounts as $account) {
        $accountSelectGroup[$account["id"]] = $account["name"];
    }

    // Form
    $form = new Form("transaction/add", "transaction/add", "Transaction");
    $form->addField(new InputDate("date", "Date", true));
    $form->addField(new OptionsSelect("account", "Account", true, $accountSelectGroup));
    $form->addField(new OptionsSelect("dest_account", "Destination Account", options: $accountSelectGroup));
    $form->addField(new InputText("destination", "Destination"));
    $form->addField(new InputText("desc", "Description"));
    $form->addField(new InputMoney("amount", "Amount", true));
    $form->addField(TransactionStatus::toOptionsSelect("status", "Status", true));
    return $form->render();
}
