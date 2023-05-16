<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

$navItems = [
    "Summary" => "summary",
    "Account" => [
        "List" => "account/list",
        "Add" => "account/add",
    ],
    "New" => [
        "Account" => "account/add",
        "Reserve" => "reserve/add",
        "Transaction" => "transaction/add",
    ],
];

$navHtml = "";
foreach ($navItems as $mainItem => $mainKey) {
    if (!is_array($mainKey)) {
        $active = $mainKey == $_GET["page"] ? " active" : null;
        $navHtml .= "
            <li class='nav-item{$active}'>
                <a class='nav-link' href='index.php?page={$mainKey}'>{$mainItem}</a>
            </li>
        ";
        continue;
    }
    $active = in_array($_GET["page"], $mainKey) ? " active" : null;
    $subNavHtml = "";
    foreach ($mainKey as $subItem => $subKey) {
        $subActive = $subKey == $_GET["page"] ? " active" : null;
        $subNavHtml .= "
            <a class='dropdown-item{$subActive}' href='index.php?page={$subKey}'>{$subItem}</a>
        ";
    }
    $navHtml .= "
            <li class='nav-item dropdown{$active}'>
                <a class='nav-link dropdown-toggle' href='#' role='button' data-toggle='dropdown' aria-expanded='false'>{$mainItem}</a>
                <div class='dropdown-menu'>
                    {$subNavHtml}
                </div>
            </li>
        ";
}

return <<<HTML
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            {$navHtml}
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
HTML;
