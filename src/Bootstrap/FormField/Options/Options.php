<?php

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField\Options;

use SierraKomodo\BudgetTracking\Bootstrap\FormField\FormField;

/**
 * Abstraction layer for form fields that have selectable options.
 */
abstract class Options extends FormField
{
    /** @var string[] $options Array of options available to the select field. */
    private array $options;

    /** @var bool $multiple If set, the field allows selecting multiple items. */
    private bool $multiple;


    public function __construct(
        string $id,
        string $label,
        bool $required = false,
        array $options = [],
        bool $multiple = false
    ) {
        $this->setOptions($options);
        $this->setMultiple($multiple);
        parent::__construct($id, $label, $required);
    }


    /**
     * Adds an item to the field's available options.
     *
     * @param string $key The internal ID passed through the form.
     * @param string $option The name displayed on the form.
     * @return void
     */
    public function addOption(string $key, string $option): void
    {
        $this->options[$key] = $option;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): void
    {
        $this->multiple = $multiple;
    }
}
