<?php

namespace SierraKomodo\BudgetTracking;


use PDO;
use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;

require_once('database.php');
require_once('common.php');
$stmt = $database->prepare("
    INSERT INTO `accounts`
        (`name`, `desc`, `account_type`)
        VALUES (:name, :desc, :account_type);
");
$stmtDelete = $database->prepare("
    DELETE FROM `accounts`
        WHERE `id` = :id;
");
$stmtCredit = $database->prepare("
    INSERT INTO `accounts_credit`
        (`id`, `limit`, `minimum_payment`, `rewards`)
        VALUES (:id, :limit, :minimum_payment, :rewards);
");


// Validate data
if ($_POST["account_type"] == "Credit") {
    if (!$_POST["limit"]) {
        $alert = new Alert("Add Account", "Limit is a required field for credit accounts.", BootstrapColor::Danger);
        $htmlOut .= $alert->render();
        $_GET["page"] = "account/add";
        return;
    }
}


// Insert account
$result = $stmt->execute([
    ":name" => $_POST["name"],
    ":desc" => $_POST["desc"] ?: null,
    ":account_type" => $_POST["account_type"],
]);
if (!$result) {
    $alert = new Alert("Add Account", "Adding account {$_POST["name"]} failed.", BootstrapColor::Danger);
    $htmlOut .= $alert->render();
    $_GET["page"] = "account/add";
    return;
}


// Insert credit account
if ($_POST["account_type"] == "Credit") {
    $account = $database->query("SELECT * FROM `accounts` ORDER BY `id` DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $result = $stmtCredit->execute([
        ":id" => $account["id"],
        ":limit" => $_POST["limit"],
        ":minimum_payment" => $_POST["minimum_payment"] ?: 0,
        ":rewards" => $_POST["rewards"] ?: 0,
    ]);
    if (!$result) {
        $stmtDelete->execute([
            ":id" => $account["id"],
        ]);
        $alert = new Alert("Add Account", "Adding credit stage for account {$_POST["name"]} failed.", BootstrapColor::Danger);
        $htmlOut .= $alert->render();
        $_GET["page"] = "account/add";
        return;
    }
}


$alert = new Alert("Add Account", "Successfully account {$_POST["name"]}.", BootstrapColor::Success);
$htmlOut .= $alert->render();
