<?php

namespace SierraKomodo\BudgetTracking\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Pure;

/**
 * Model for `accounts_credit` table entries.
 */
#[Entity]
#[Table(name: 'accounts_credit')]
class AccountCredit
{
    // Properties
    /** @var int $id Primary key. */
    #[Column(type: Types::INTEGER)]
    #[Id]
    #[GeneratedValue]
    private int $id;


    /** @var float $limit Credit account limit. */
    #[Column(name: '`limit`', type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $limit;


    /** @var ?float $minimumPayment Minimum monthly payment amount. */
    #[Column(name: 'minimum_payment', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?float $minimumPayment = null;


    /** @var ?float $rewards Available credit rewards. */
    #[Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?float $rewards = null;


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


    /**
     * Fetches {@link self::$limit}
     *
     * @return float
     */
    #[Pure] public function getLimit(): float
    {
        return $this->limit;
    }


    /**
     * Sets {@link self::$limit}.
     *
     * @param float $limit
     *
     * @return void
     */
    public function setLimit(float $limit): void
    {
        $this->limit = $limit;
    }


    /**
     * Fetches {@link self::$minimumPayment}.
     *
     * @return ?float
     */
    #[Pure] public function getMinimumPayment(): ?float
    {
        return $this->minimumPayment;
    }


    /**
     * Sets {@link self::$minimumPayment}.
     *
     * @param ?float $minimumPayment
     *
     * @return void
     */
    public function setMinimumPayment(?float $minimumPayment = null): void
    {
        $this->minimumPayment = $minimumPayment;
    }


    /**
     * Fetches {@link self::$rewards}.
     *
     * @return ?float
     */
    #[Pure] public function getRewards(): ?float
    {
        return $this->rewards;
    }


    /**
     * Sets {@link self::$rewards}.
     *
     * @param ?float $rewards
     *
     * @return void
     */
    public function setRewards(?float $rewards): void
    {
        $this->rewards = $rewards;
    }
}
