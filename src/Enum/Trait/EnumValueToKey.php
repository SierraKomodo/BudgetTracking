<?php

namespace SierraKomodo\BudgetTracking\Enum\Trait;

/**
 * Trait that provides the `toKey()` public function.
 */
trait EnumValueToKey
{
    /**
     * Converts an enumeration value to a valid and standards-confirming key value.
     *
     * @return string Converted value.
     */
    public function toKey(): string
    {
        return strtolower($this->value);
    }
}
