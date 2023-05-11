<?php

namespace SierraKomodo\BudgetTracking\Factory;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;

class EntityManagerFactory
{
    private static EntityManagerInterface $entityManager;


    /**
     * Retrieves a cached instance of {@link EntityManager}, creating one if it doesn't already exist.
     *
     * **NOTE**: This also instantiates {@link DatabaseConnectionFactory} with the same config as the entity manager.
     *
     * @return EntityManagerInterface
     * @throws Exception
     * @throws MissingMappingDriverImplementation
     */
    public static function getEntityManager(): EntityManagerInterface
    {
        if (empty(self::$entityManager)) {
            $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../Model']);
            $conn = DatabaseConnectionFactory::getConnection($config);
            self::$entityManager = new EntityManager($conn, $config);
        }
        return self::$entityManager;
    }
}
