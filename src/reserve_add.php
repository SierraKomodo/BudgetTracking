<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputMoney;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;
use SierraKomodo\BudgetTracking\Repository\AccountRepository;


function renderReserveAdd(): string
{
    // Fetch and compile data
    $entityManager = EntityManagerFactory::getEntityManager();
    /** @var AccountRepository $accountRepository */
    $accountRepository = $entityManager->getRepository(Account::class);

    // Form
    $form = new Form("reserve/add", "reserve/add", "Reserve");
    $form->addField(new InputText("desc", "Description", true));
    $form->addField($accountRepository->toOptionsSelect());
    $form->addField(new InputMoney("amount", "Amount", true));
    return $form->render();
}
