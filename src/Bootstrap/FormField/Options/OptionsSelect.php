<?php

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField\Options;

/**
 * Select field.
 */
class OptionsSelect extends Options
{
    /**
     * @inheritDoc
     */
    protected function _renderInput(): string
    {
        $attributesRendered = $this->_renderAttributes();

        // Options & Default Option
        $optionsRendered = "";
        foreach ($this->getOptions() as $id => $label) {
            $selected = null;
            if ($this->getDefaultValue() == $id) {
                $selected = " selected";
            }
            $optionsRendered .= "<option value='{$id}'{$selected}>{$label}</option>";
        }

        return "
            <select class='form-control' id='{$this->getId()}' name='{$this->getId()}' {$attributesRendered}>
                {$optionsRendered}
            </select>
        ";
    }
}
