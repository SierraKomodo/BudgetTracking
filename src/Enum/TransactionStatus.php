<?php

namespace SierraKomodo\BudgetTracking\Enum;

use JetBrains\PhpStorm\Pure;
use SierraKomodo\BudgetTracking\Enum\Trait\EnumToHtmlForm;
use SierraKomodo\BudgetTracking\Enum\Trait\EnumValueToKey;

enum TransactionStatus: string
{
    use EnumToHtmlForm;
    use EnumValueToKey;

    case Planned = "Planned";
    case Pending = "Pending";
    case Processed = "Processed";


    /**
     * Returns a bootstrap color based on the current transaction status.
     *
     * @return BootstrapColor
     */
    #[Pure] public function toBootstrapColor(): BootstrapColor
    {
        return match ($this) {
            self::Planned => BootstrapColor::Info,
            self::Pending => BootstrapColor::Warning,
            self::Processed => BootstrapColor::Success
        };
    }
}
