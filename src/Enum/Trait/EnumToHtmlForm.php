<?php

namespace SierraKomodo\BudgetTracking\Enum\Trait;

use SierraKomodo\BudgetTracking\Bootstrap\FormField\Input\InputDate;
use SierraKomodo\BudgetTracking\Bootstrap\FormField\Options\OptionsSelect;

/**
 * Trait that allows generating various HTML form fields.
 */
trait EnumToHtmlForm
{
    /**
     * Creates an OptionsSelect form field using the enumeration's possible values as options.
     *
     * @param string $id Field ID.
     * @param string $label Field label.
     * @param bool $required Whether the field is required.
     * @return OptionsSelect The created OptionsSelect object.
     */
    public static function toOptionsSelect(string $id = "enum", string $label = "Enum Select", bool $required = FALSE): OptionsSelect
    {
        $options = [];
        foreach (self::cases() as $type) {
            $options[$type->value] = $type->value;
        }
        return new OptionsSelect($id, $label, $required, $options);
    }
}
