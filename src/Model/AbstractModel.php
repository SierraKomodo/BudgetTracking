<?php

namespace SierraKomodo\BudgetTracking\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use JetBrains\PhpStorm\Pure;

abstract class AbstractModel
{
    // Properties

    /** @var int $id Primary key. */
    #[Column(type: Types::INTEGER)]
    #[Id]
    #[GeneratedValue]
    protected int $id;


    // Getters and Setters

    /**
     * Fetches {@link self::$id}.
     *
     * @return int
     */
    #[Pure] public function getId(): int
    {
        return $this->id;
    }
}
