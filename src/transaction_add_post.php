<?php

namespace SierraKomodo\BudgetTracking;

use PDO;

require_once('database.php');
require_once('common.php');
$stmt = $database->prepare("
    INSERT INTO `transactions`
        (`date`, `account`, `dest_account`, `destination`, `desc`, `amount`, `status`)
        VALUES (:date, :account, :dest_account, :destination, :desc, :amount, :status);
");
$stmtFetchAccount = $database->prepare("
    SELECT `name` FROM `accounts`
        WHERE `id` = :id;
");


// Validate data
if (!$_POST["dest_account"] && !$_POST["destination"]) {
    $postResult["status"] = "Fail";
    $postResult["text"] = "Either destination or destination account is required.";
    $_GET["page"] = "transaction/add";
    return;
}
if ($_POST["dest_account"] && !$_POST["destination"]) {
    $stmtFetchAccount->execute([
        ":id" => $_POST["dest_account"],
    ]);
    $account = $stmtFetchAccount->fetch(PDO::FETCH_ASSOC);
    $_POST["destination"] = $account["name"];
}


// Insert transaction
$result = $stmt->execute([
    ":date" => $_POST["date"],
    ":account" => $_POST["account"],
    ":dest_account" => $_POST["dest_account"] ?: null,
    ":destination" => $_POST["destination"],
    ":desc" => $_POST["desc"] ?: null,
    ":amount" => $_POST["amount"],
    ":status" => $_POST["status"],
]);
if (!$result) {
    $postResult["status"] = "Fail";
    $postResult["text"] = "Failed to create the new transaction entry.";
    $_GET["page"] = "transaction/add";
    return;
}
$postResult["status"] = "Success";
$postResult["text"] = "Transaction successfully added.";
