<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;

require_once('../vendor/autoload.php');

require_once('../src/render_html.php');
$htmlOut = "";


if (!empty($_POST)) {
    if (!isset($_GET["post"])) {
        $_GET["post"] = "";
    }
    switch ($_GET["post"]) {
        case "account/add":
            require_once('../src/account_add_post.php');
            break;

        case "reserve/add":
            require_once('../src/reserve_add_post.php');
            break;

        case "transaction/add":
            require_once('../src/transaction_add_post.php');
            break;

        case "":
            // Do nothing
            break;

        default:
            $_GET["page"] = "404.php";
    }
}


if (!isset($_GET["page"])) {
    $_GET["page"] = "";
}
switch ($_GET["page"]) {
    case "":
    case "summary":
        require_once("../src/summary.php");
        $htmlOut .= renderSummary();
        break;

    case "account/add":
        require_once('../src/account_add.php');
        $htmlOut .= renderAccountAdd();
        break;

    case "reserve/add":
        require_once('../src/reserve_add.php');
        $htmlOut .= renderReserveAdd();
        break;

    case "transaction/add":
        require_once("../src/transaction_add.php");
        $htmlOut .= renderAddTransaction();
        break;

    case "transaction/list":
        require_once("../src/transaction_list.php");
        $htmlOut .= renderTransactionList($_GET["account"]);
        break;

    default:
        require_once("../src/404.php");
        $htmlOut .= render404();
}

echo renderHtml($htmlOut);
