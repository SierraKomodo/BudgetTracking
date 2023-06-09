<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField\Input;

/**
 * Input type number.
 */
class InputNumber extends Input
{
    protected string $inputType = "number";

    protected array $attributes
        = [
            "placeholder" => "0",
        ];
}
