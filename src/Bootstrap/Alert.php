<?php

namespace SierraKomodo\BudgetTracking\Bootstrap;

use SierraKomodo\BudgetTracking\Enum\BootstrapColor;

/**
 * Generation for boostrap alerts.
 */
class Alert
{
    /** @var string $title Alert's title. */
    protected string $title;

    /** @var string $content Alert's content. */
    protected string $content;

    /** @var BootstrapColor $color Alert's CSS color class. */
    protected BootstrapColor $color;


    /**
     * Alert constructor.
     *
     * @param string         $title   Alert's title.
     * @param string         $content Alert's content.
     * @param BootstrapColor $color   Alert's CSS color class.
     */
    public function __construct(
        string $title,
        string $content,
        BootstrapColor $color = BootstrapColor::Primary
    ) {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setColor($color);
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setColor(BootstrapColor $color): void
    {
        $this->color = $color;
    }

    /**
     * Renders the alert into HTML.
     *
     * @return string The rendered HTML.
     */
    public function render(): string
    {
        return "
            <div class='alert alert-{$this->color->toKey()}'>
                <h4>{$this->title}</h4>
                <p>{$this->content}</p>
            </div>
        ";
    }
}
