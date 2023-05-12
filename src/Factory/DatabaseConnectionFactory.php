<?php

namespace SierraKomodo\BudgetTracking\Factory;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use JetBrains\PhpStorm\Pure;
use RuntimeException;

/**
 * Static factory to instantiate a {@link Connection} connection, or return an existing connection if already
 * instanced.
 */
class DatabaseConnectionFactory
{
    /**
     * @var Connection $connection Cached {@link Connection} instance.
     */
    private static Connection $connection;


    /**
     * Retrieves a cached instance of {@link Connection}, creating one if it doesn't already exist.
     *
     * @param Configuration|null $config
     *
     * @return Connection|false
     * @throws Exception from {@link DriverManager::getConnection()}
     * @throws RuntimeException if connection has not yet been instantiated by {@link EntityManagerFactory}.
     */
    public static function getConnection(?Configuration $config = null
    ): Connection|false {
        if (empty(self::$connection)) {
            if (!$config) {
                throw new RuntimeException(
                    "Attempted to access an uninstantiated database connection without a valid config. Connection must be instantiated with the EntityManagerFactory before use."
                );
            }
            self::$connection = DriverManager::getConnection(
                self::getDriverParams()
            );
        }
        return self::$connection;
    }


    /**
     * Fetches an array of relevant connection parameters from {@link $_ENV}.
     *
     * @return string[]|false
     */
    #[Pure] protected static function getDriverParams(): array|false
    {
        return match ($_ENV['DB_DRIVER']) {
            'pdo_sqlite' => [
                'driver' => 'pdo_sqlite',
                'user' => $_ENV['DB_USER'] ?: null,
                'password' => $_ENV['DB_PASSWORD'] ?: null,
                'path' => $_ENV['DB_PATH'] ?: null,
                'memory' => $_ENV['DB_MEMORY'] ?: null,
            ],
            'pdo_mysql' => [
                'driver' => 'pdo_mysql',
                'user' => $_ENV['DB_USER'] ?: null,
                'password' => $_ENV['DB_PASSWORD'] ?: null,
                'host' => $_ENV['DB_HOST'] ?: null,
                'port' => $_ENV['DB_PORT'] ?: null,
                'dbname' => $_ENV['DB_DBNAME'] ?: null,
                'unix_socket' => $_ENV['DB_UNIX_SOCKET'] ?: null,
                'charset' => $_ENV['DB_CHARSET'] ?: null,
            ],
            'pdo_pgsql' => [
                'driver' => 'pdo_pgsql',
                'user' => $_ENV['DB_USER'] ?: null,
                'password' => $_ENV['DB_PASSWORD'] ?: null,
                'host' => $_ENV['DB_HOST'] ?: null,
                'port' => $_ENV['DB_PORT'] ?: null,
                'dbname' => $_ENV['DB_DBNAME'] ?: null,
                'charset' => $_ENV['DB_CHARSET'] ?: null,
                'sslmode' => $_ENV['DB_SSL_MODE'] ?: null,
                'sslrootcert' => $_ENV['DB_SSL_ROOTCERT'] ?: null,
                'sslcert' => $_ENV['DB_SSL_CERT'] ?: null,
                'sslkey' => $_ENV['DB_SSL_KEY'] ?: null,
                'sslcrl' => $_ENV['DB_SSL_CRL'] ?: null,
                'application_name' => $_ENV['DB_APPLICATION_NAME'] ?: null,
            ],
            default => false,
        };
    }
}
