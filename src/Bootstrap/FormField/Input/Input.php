<?php

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField\Input;

use SierraKomodo\BudgetTracking\Bootstrap\FormField\FormField;

abstract class Input extends FormField
{
    /** @var string $inputType The input field's type attribute. */
    protected string $inputType = "text";


    protected function _renderInput(): string
    {
        $attributesRendered = $this->_renderAttributes();
        return "
            <input class='form-control' type='{$this->getInputType()}' id='{$this->getId()}' name='{$this->getId()}' {$attributesRendered} />
        ";
    }


    public function getInputType(): string
    {
        return $this->inputType;
    }


    public function setDefaultValue(string $defaultValue): void
    {
        $this->setAttribute("value", $defaultValue);
        parent::setDefaultValue($defaultValue);
    }
}
