<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Factory\EntityManagerFactory;

require_once(__DIR__ . '/../src/bootstrap.php');

require_once(__DIR__ . '/../src/render_html.php');
$htmlOut = "";


$entityManager = EntityManagerFactory::getEntityManager();


if (!empty($_POST) && !empty($_GET['post'])) {
    $postResult = [];
    switch ($_GET["post"]) {
        case "account/add":
            $htmlOut .= require_once(__DIR__ . '/../src/account_add_post.php');
            break;

        case "transaction/add":
            $htmlOut .= require_once(__DIR__
                . '/../src/transaction_add_post.php');
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
        require_once(__DIR__ . '/../src/summary.php');
        $htmlOut .= renderSummary();
        break;

    case "account/add":
        require_once(__DIR__ . '/../src/account_add.php');
        $htmlOut .= renderAccountAdd();
        break;

    case 'account/list':
        require_once(__DIR__ . '/../src/account_list.php');
        $htmlOut .= renderAccountList();
        break;

    case "transaction/add":
        require_once(__DIR__ . '/../src/transaction_add.php');
        $htmlOut .= renderAddTransaction();
        break;

    case "transaction/list":
        require_once(__DIR__ . '/../src/transaction_list.php');
        $htmlOut .= renderTransactionList($_GET["account"]);
        break;

    default:
        require_once(__DIR__ . '/../src/404.php');
        $htmlOut .= render404();
}

echo renderHtml($htmlOut);
