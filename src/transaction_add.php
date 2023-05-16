<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Form;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputDate;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputMoney;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputText;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;
use SierraKomodo\BudgetTracking\Model\Account;
use SierraKomodo\BudgetTracking\Repository\AccountRepository;


function renderAddTransaction(): string
{
    // Fetch and compile data
    $entityManager = EntityManagerFactory::getEntityManager();
    /** @var AccountRepository $accountRepository */
    $accountRepository = $entityManager->getRepository(Account::class);

    // Form
    $form = new Form("transaction/add", "transaction/add", "Transaction");
    $form->addField(new InputDate("date", "Date", true));
    $form->addField($accountRepository->toOptionsSelect());
    $form->addField(
        $accountRepository->toOptionsSelect(
            'dest_account',
            'Destination Account',
            false
        )
    );
    $form->addField(new InputText("destination", "Destination"));
    $form->addField(new InputText("desc", "Description"));
    $form->addField(new InputMoney("amount", "Amount", true));
    $form->addField(
        TransactionStatus::toOptionsSelect("status", "Status", true)
    );
    return $form->render();
}
