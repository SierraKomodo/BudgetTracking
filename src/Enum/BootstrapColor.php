<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking\Enum;

use SierraKomodo\BudgetTracking\Enum\Trait\EnumValueToKey;

enum BootstrapColor: string
{
    use EnumValueToKey;

    case Primary = "primary";
    case Secondary = "secondary";
    case Success = "success";
    case Danger = "danger";
    case Warning = "warning";
    case Info = "info";
    case Light = "light";
    case Dark = "dark";
}
