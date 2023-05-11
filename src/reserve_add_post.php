<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;
use SierraKomodo\BudgetTracking\Factory\DatabaseConnectionFactory;

require_once(__DIR__ . '/common.php');


// Insert transaction
$conn = DatabaseConnectionFactory::getConnection();
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
