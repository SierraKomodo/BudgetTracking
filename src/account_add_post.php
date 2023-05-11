<?php

namespace SierraKomodo\BudgetTracking;

use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;
use SierraKomodo\BudgetTracking\Factory\DatabaseConnectionFactory;


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
$conn = DatabaseConnectionFactory::getConnection();
$result = $conn->executeStatement(
    "
        INSERT INTO `accounts` (`name`, `desc`, `account_type`)
        VALUES (:name, :desc, :account_type);
    ",
    [
        "name" => $_POST["name"],
        "desc" => $_POST["desc"] ?: null,
        "account_type" => $_POST["account_type"]
    ]
);
if (!$result) {
    $alert = new Alert("Add Account", "Adding account {$_POST["name"]} failed.", BootstrapColor::Danger);
    $htmlOut .= $alert->render();
    $_GET["page"] = "account/add";
    return;
}


// Insert credit account
if ($_POST["account_type"] == "Credit") {
    $account = $conn->fetchAssociative(
        "
        SELECT *
        FROM `accounts`
        ORDER BY `id` DESC
        LIMIT 1;
    "
    );
    $result = $conn->executeStatement(
        "
            INSERT INTO `accounts_credit` (`id`, `limit`, `minimum_payment`, `rewards`)
            VALUES (:id, :limit, :minimum_payment, :rewards);
        ",
        [
            "id" => $account["id"],
            "limit" => $_POST["limit"],
            "minimum_payment" => $_POST["minimum_payment"] ?: 0,
            "rewards" => $_POST["rewards"] ?: 0,
        ]
    );
    if (!$result) {
        $conn->executeStatement(
            "
                DELETE FROM `accounts` WHERE `id` = :id;
            ",
            [
                "id" => $account["id"],
            ]
        );
        $alert = new Alert(
            "Add Account",
            "Adding credit stage for account {$_POST["name"]} failed.",
            BootstrapColor::Danger
        );
        $htmlOut .= $alert->render();
        $_GET["page"] = "account/add";
        return;
    }
}


$alert = new Alert("Add Account", "Successfully account {$_POST["name"]}.", BootstrapColor::Success);
$htmlOut .= $alert->render();
