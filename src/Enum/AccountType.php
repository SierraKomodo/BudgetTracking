<?php

namespace SierraKomodo\BudgetTracking\Enum;


use SierraKomodo\BudgetTracking\Enum\Trait\EnumToHtmlForm;
use SierraKomodo\BudgetTracking\Enum\Trait\EnumValueToKey;

/**
 * Enumerations for account types. Values match those found in the `accounts.account_type` table field.
 */
enum AccountType: string
{
    use EnumToHtmlForm;
    use EnumValueToKey;


    case Cash = "Cash";
    case Credit = "Credit";
    case Reserve = "Reserve";
    case Other = "Other";
}
