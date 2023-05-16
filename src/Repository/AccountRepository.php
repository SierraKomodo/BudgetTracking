<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking\Repository;

use Doctrine\ORM\EntityRepository;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Options\OptionsSelect;
use SierraKomodo\BudgetTracking\Model\Account;

/**
 * Repository for {@link Account}.
 */
class AccountRepository extends EntityRepository
{
    // Helpers

    /**
     * Generates an {@link OptionsSelect} object containing all accounts.
     *
     * @param string $id       Field ID, passed to {@link OptionsSelect::__construct()}.
     * @param string $label    Field label, passed to {@link OptionsSelect::__construct()}.
     * @param bool   $required Whether the field is required, passed to {@link OptionsSelect::__construct()}.
     *
     * @return OptionsSelect
     */
    public function toOptionsSelect(
        string $id = 'account',
        string $label = 'Account',
        bool $required = true
    ): OptionsSelect {
        $options = [];
        foreach ($this->findAll() as $account) {
            $options[$account->getId()] = $account->getName();
        }
        asort($options);
        return new OptionsSelect($id, $label, $required, $options);
    }
}
