<?php

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField\Input;

/**
 * Input type date.
 */
class InputDate extends Input
{
    protected string $inputType = "date";


    public function __construct(
        string $id,
        string $label,
        bool $required = false
    ) {
        parent::__construct($id, $label, $required);
        if (!$this->getDefaultValue()) {
            $this->setDefaultValue(date("m/d/Y"));
        }
    }
}
