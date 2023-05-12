<?php

namespace SierraKomodo\BudgetTracking\Bootstrap\FormField;

/**
 * Generation for form fields.
 */
abstract class FormField
{
    /** @var string $label The field's label. */
    protected string $label;
    /** @var string[] $attributes Map of HTML attributes applied to the field. */
    protected array $attributes = [];
    /** @var null|string $defaultValue If set, the field's default value will be this on load. */
    protected ?string $defaultValue = null;
    /** @var null|string $prependText If set, the field will have prepended text with the contents. */
    protected ?string $prependText = null;
    /** @var string $id The field's internal ID key. */
    private string $id;

    public function __construct(
        string $id,
        string $label,
        bool $required = false
    ) {
        $this->setId($id);
        $this->setLabel($label);
        if ($required) {
            $this->setAttribute("required");
        }
    }

    public function setAttribute(
        string $attribute,
        string $value = "true"
    ): void {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Renders the form field as an HTML `form-group` block.
     *
     * @return string The rendered HTML block.
     */
    public function render(): string
    {
        if ($this->getPrependText()) {
            return "
                <div class='form-group'>
                    <label for='{$this->getId()}'>{$this->getLabel()}</label>
                    <div class='input-group'>
                        <div class='input-group-prepend'>
                            <div class='input-group-text'>{$this->getPrependText()}</div>
                        </div>
                        " . $this->_renderInput() . "
                    </div>
                </div>
            ";
        }
        return "
            <div class='form-group'>
                <label for='{$this->getId()}'>{$this->getLabel()}</label>
                " . $this->_renderInput() . "
            </div>
        ";
    }

    public function getPrependText(): ?string
    {
        return $this->prependText;
    }

    public function setPrependText(string $prependText): void
    {
        $this->prependText = $prependText;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * Handles generating the input for the form field.
     *
     * @return string The rendered HTML input.
     */
    abstract protected function _renderInput(): string;

    public function clearAttribute(string $attribute): void
    {
        unset($this->attributes[$attribute]);
    }

    /**
     * Fetches the value of the given attribute.
     *
     * @param string $attribute
     *
     * @return string|null
     */
    public function getAttribute(string $attribute): ?string
    {
        if (!isset($this->attributes[$attribute])) {
            return null;
        }
        return $this->attributes[$attribute];
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * Generates a string of HTML formatted attributes for the form field.
     *
     * @return string The rendered attribute string.
     */
    protected function _renderAttributes(): string
    {
        $renderedAttributes = [];
        foreach ($this->getAttributes() as $attribute => $value) {
            $renderedAttributes[] = "{$attribute}='{$value}'";
        }
        return implode(" ", $renderedAttributes);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
