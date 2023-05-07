<?php

namespace SierraKomodo\BudgetTracking;

use PDO;
use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputMoney;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Options\OptionsSelect;

require_once('database.php');


function renderReserveAdd(): string
{
    global $database;

    // Fetch and compile data
    $accounts = $database->query("SELECT * FROM `accounts`;")->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $accountSelectGroup = [];
    foreach ($accounts as $account) {
        $accountSelectGroup[$account["id"]] = $account["name"];
    }

    // Form
    $form = new Form("reserve/add", "reserve/add", "Reserve");
    $form->addField(new InputText("desc", "Description", TRUE));
    $form->addField(new OptionsSelect("account", "Account", TRUE, $accountSelectGroup));
    $form->addField(new InputMoney("amount", "Amount", TRUE));
    return $form->render();
}
