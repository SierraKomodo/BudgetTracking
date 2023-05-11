<?php

namespace SierraKomodo\BudgetTracking\Bootstrap;

use SierraKomodo\BudgetTracking\Bootstrap\FormField\FormField;

/**
 * Generation for forms.
 */
class Form
{
    /** @var string $postTarget The form's target for `post`. */
    protected string $postTarget;

    /** @var string $pageTarget The form's target for `page`. */
    protected string $pageTarget;

    /** @var FormField[][] This form's fields and sections. */
    protected array $formFields = [];

    protected string $currentSection;


    public function __construct(
        string $postTarget,
        string $pageTarget = "summary",
        string $defaultSection = "Default Form Section"
    ) {
        $this->postTarget = $postTarget;
        $this->pageTarget = $pageTarget;
        $this->currentSection = $defaultSection;
    }


    /**
     * Adds a form field to the form.
     *
     * @param FormField $newField The field to add.
     * @return void
     */
    public function addField(FormField $newField): void
    {
        $this->formFields[$this->currentSection][] = $newField;
    }


    /**
     * Starts a new section in the form.
     *
     * @param string $section The section header.
     * @return void
     */
    public function addSection(string $section): void
    {
        $this->currentSection = $section;
    }


    /**
     * Renders the full form out in HTML.
     *
     * @return string The fully rendered HTML form.
     */
    public function render(): string
    {
        $renderedSections = [];
        foreach ($this->formFields as $section => $formFields) {
            $renderedSection = "<h3>{$section}</h3>";
            foreach ($formFields as $formField) {
                $renderedSection .= $formField->render();
            }
            $renderedSections[] = $renderedSection;
        }
        $renderedForm = implode("<br />", $renderedSections);

        return "
            <form action='index.php?page={$this->pageTarget}&post={$this->postTarget}' method='post'>
                {$renderedForm}<hr />
                <div class='form-group'>
                    <button type='submit' class='form-control btn btn-primary'>Submit</button>
                </div>
            </form>
        ";
    }
}
