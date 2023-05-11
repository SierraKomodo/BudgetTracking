<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputMoney;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Options\OptionsSelect;

require_once('database.php');


function renderReserveAdd(): string
{
    global $conn;

    // Fetch and compile data
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
    $form = new Form("reserve/add", "reserve/add", "Reserve");
    $form->addField(new InputText("desc", "Description", true));
    $form->addField(new OptionsSelect("account", "Account", true, $accountSelectGroup));
    $form->addField(new InputMoney("amount", "Amount", true));
    return $form->render();
}
