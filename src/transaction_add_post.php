<?php

namespace SierraKomodo\BudgetTracking;

global $conn;

require_once('database.php');


// Validate data
if (!$_POST["dest_account"] && !$_POST["destination"]) {
    $postResult["status"] = "Fail";
    $postResult["text"] = "Either destination or destination account is required.";
    $_GET["page"] = "transaction/add";
    return;
}
if ($_POST["dest_account"] && !$_POST["destination"]) {
    $account = $conn->fetchAssociative(
        "
            SELECT `name`
            FROM `accounts`
            WHERE `id` = :id;
        ",
        [
            "id" => $_POST["dest_account"],
        ]
    );
    $_POST["destination"] = $account["name"];
}


// Insert transaction
$result = $conn->executeStatement(
    "
        INSERT INTO `transactions` (`date`, `account`, `dest_account`, `destination`, `desc`, `amount`, `status`)
        VALUES (:date, :account, :dest_account, :destination, :desc, :amount, :status);
    ",
    [
        "date" => $_POST["date"],
        "account" => $_POST["account"],
        "dest_account" => $_POST["dest_account"] ?: null,
        "destination" => $_POST["destination"],
        "desc" => $_POST["desc"] ?: null,
        "amount" => $_POST["amount"],
        "status" => $_POST["status"],
    ]
);
if (!$result) {
    $postResult["status"] = "Fail";
    $postResult["text"] = "Failed to create the new transaction entry.";
    $_GET["page"] = "transaction/add";
    return;
}
$postResult["status"] = "Success";
$postResult["text"] = "Transaction successfully added.";
