<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputMoney;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Enum\AccountType;


function renderAccountAdd(): string
{
    // Form
    $form = new Form("account/add", "account/add", "Account");
    $form->addField(new InputText("name", "Name", true));
    $form->addField(new InputText("desc", "Description"));
    $form->addField(AccountType::toOptionsSelect("account_type", "Account Type", true));
    $form->addSection("Credit");
    $form->addField(new InputMoney("limit", "Credit Limit"));
    $form->addField(new InputMoney("minimum_payment", "Minimum Monthly Payment"));
    $form->addField(new InputMoney("rewards", "Rewards"));
    return $form->render();
}
