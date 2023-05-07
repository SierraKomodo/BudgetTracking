<?php

namespace SierraKomodo\BudgetTracking;

global $conn;

use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;

require_once('database.php');
require_once('common.php');


// Insert transaction
$result = $conn->executeStatement(
    "
        INSERT INTO `reserves` (`desc`, `account`, `amount`)
        VALUES (:desc, :account, :amount);
    ", [
        "desc" => $_POST["desc"],
        "account" => $_POST["account"],
        "amount" => $_POST["amount"],
    ]
);
if (!$result) {
    $alert = new Alert("Add Reserve", "Adding reserve {$_POST["desc"]} failed.", BootstrapColor::Danger);
    $htmlOut .= $alert->render();
    $_GET["page"] = "reserve/add";
    return;
}
$alert = new Alert("Add Reserve", "Reserve {$_POST["desc"]} successfully added.", BootstrapColor::Success);
$htmlOut .= $alert->render();
