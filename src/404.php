<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

function render404(): string
{
    return <<<HTML
<h1>404 Page Not Found</h1>
HTML;
}
