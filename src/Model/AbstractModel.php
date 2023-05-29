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


    // Public Methods

    public function renderControlButtons(): string
    {
        return "
            <a href='#' class='btn btn-sm btn-primary' data-toggle='tooltip' title='View'><i class='fa-solid fa-magnifying-glass'></i></a>
            <a href='#' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Edit'><i class='fa-solid fa-wrench'></i></a>
            <a href='#' class='btn btn-sm btn-danger' data-toggle='tooltip' title='Delete'><i class='fa-solid fa-trash'></i></a>
        ";
    }


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
