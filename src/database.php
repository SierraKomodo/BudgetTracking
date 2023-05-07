<?php

namespace SierraKomodo\BudgetTracking;

use PDO;

require_once('environment.php');

/** @var PDO $database Primary database connection. */
$database = new PDO("mysql:host={$_ENV["MYSQL_HOST"]}:{$_ENV["MYSQL_PORT"]};dbname={$_ENV["MYSQL_DB"]}", $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASS"]);
