<?php

namespace SierraKomodo\BudgetTracking\Enum;

use SierraKomodo\BudgetTracking\Enum\Trait\EnumToHtmlForm;
use SierraKomodo\BudgetTracking\Enum\Trait\EnumValueToKey;

enum TransactionStatus: string
{
    use EnumToHtmlForm;
    use EnumValueToKey;

    case Planned = "Planned";
    case Pending = "Pending";
    case Processed = "Processed";
}
