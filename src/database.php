<?php

namespace SierraKomodo\BudgetTracking;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use SierraKomodo\BudgetTracking\Bootstrap\Alert;
use SierraKomodo\BudgetTracking\Enum\BootstrapColor;

require_once('environment.php');


switch ($_ENV["DB_DRIVER"]) {
    case "pdo_sqlite":
        $connParams = [
            "user"     => $_ENV["DB_USER"] ?: null,
            "password" => $_ENV["DB_PASSWORD"] ?: null,
            "path"     => $_ENV["DB_PATH"] ?: null,
            "memory"   => $_ENV["DB_MEMORY"] ?: null,
        ];
        break;
    
    case "pdo_mysql":
        $connParams = [
            "user"        => $_ENV["DB_USER"] ?: null,
            "password"    => $_ENV["DB_PASSWORD"] ?: null,
            "host"        => $_ENV["DB_HOST"] ?: null,
            "port"        => $_ENV["DB_PORT"] ?: null,
            "dbname"      => $_ENV["DB_DBNAME"] ?: null,
            "unix_socket" => $_ENV["DB_UNIX_SOCKET"] ?: null,
            "charset"     => $_ENV["DB_CHARSET"] ?: null,
        ];
        break;
    
    case "pdo_pgsql":
        $connParams = [
            "user"             => $_ENV["DB_USER"] ?: null,
            "password"         => $_ENV["DB_PASSWORD"] ?: null,
            "host"             => $_ENV["DB_HOST"] ?: null,
            "port"             => $_ENV["DB_PORT"] ?: null,
            "dbname"           => $_ENV["DB_DBNAME"] ?: null,
            "charset"          => $_ENV["DB_CHARSET"] ?: null,
            "sslmode"          => $_ENV["DB_SSL_MODE"] ?: null,
            "sslrootcert"      => $_ENV["DB_SSL_ROOTCERT"] ?: null,
            "sslcert"          => $_ENV["DB_SSL_CERT"] ?: null,
            "sslkey"           => $_ENV["DB_SSL_KEY"] ?: null,
            "sslcrl"           => $_ENV["DB_SSL_CRL"] ?: null,
            "application_name" => $_ENV["DB_APPLICATION_NAME"] ?: null,
        ];
        break;
    
    default:
        $alert   = new Alert(
            "Database Failure",
            "The database failed to initialize: Invalid DB driver.",
            BootstrapColor::Danger
        );
        $htmlOut .= $alert->render();
        return;
}
$connParams["driver"] = $_ENV["DB_DRIVER"];

try {
    $conn = DriverManager::getConnection($connParams);
} catch (Exception $e) {
    $alert   = new Alert(
        "Database Failure",
        "The database failed to initialize: {$e->getMessage()}.",
        BootstrapColor::Danger
    );
    $htmlOut .= $alert->render();
}
