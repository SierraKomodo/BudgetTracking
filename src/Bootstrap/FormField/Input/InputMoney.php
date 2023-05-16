<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField\Input;

class InputMoney extends Input
{
    protected string $inputType = "number";

    protected ?string $prependText = "$";

    protected array $attributes
        = [
            "placeholder" => "0.00",
            "step" => ".01",
        ];
}
