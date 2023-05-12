<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Pure;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;

/**
 * Model for `transactions` table.
 */
#[Entity]
#[Table(name: 'transactions')]
class Transaction
{
    // Properties

    /** @var int $id Primary key. */
    #[Column(type: Types::INTEGER)]
    #[Id]
    #[GeneratedValue]
    private int $id;


    /** @var DateTimeImmutable $date Date of the transaction. */
    #[Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $date;


    /** @var Account $account Source {@link Account} reference. */
    #[ManyToOne(targetEntity: Account::class, inversedBy: 'transactions')]
    #[JoinColumn(name: 'account', referencedColumnName: 'id')]
    private Account $account;


    /** @var ?Account $destAccount Destination {@link Account} reference for transfers. */
    #[ManyToOne(targetEntity: Account::class, inversedBy: 'transfers')]
    #[JoinColumn(name: 'dest_account', referencedColumnName: 'id', nullable: true)]
    private ?Account $destAccount = null;


    /** @var string|null $destination Destination for non-transfers. Overridden if {@link self::$destAccount} is set. */
    #[Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $destination = null;


    /** @var string|null $desc Transaction description. */
    #[Column(name: '`desc`', type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $desc = null;


    /** @var float $amount Dollar amount of the transaction. */
    #[Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $amount;


    /** @var TransactionStatus $status Transaction's status. */
    #[Column(type: Types::TEXT, length: 255, enumType: TransactionStatus::class)]
    private TransactionStatus $status;


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
     * Fetches {@link self::date}.
     *
     * @return DateTimeImmutable
     */
    #[Pure] public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }


    /**
     * Sets {@link self::date}.
     *
     * @param DateTimeInterface $date
     *
     * @return void
     */
    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }


    /**
     * Fetches {@link self::$account}.
     *
     * @return Account
     */
    #[Pure] public function getAccount(): Account
    {
        return $this->account;
    }


    /**
     * Sets {@link self::$account}.
     *
     * @param Account $account
     *
     * @return void
     */
    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }


    /**
     * Fetches {@link self::$destAccount}.
     *
     * @return Account
     */
    #[Pure] public function getDestAccount(): Account
    {
        return $this->destAccount;
    }


    /**
     * Sets {@link self::$destAccount}.
     *
     * @param Account $account
     *
     * @return void
     */
    public function setDestAccount(Account $account): void
    {
        $this->destAccount = $account;
    }


    /**
     * Fetches {@link self::$destination}.
     *
     * If {@link self::$destAccount} is set, fetches the account's name instead.
     *
     * @return string
     */
    #[Pure] public function getDestination(): string
    {
        if ($this->destAccount) {
            return $this->destAccount->getName();
        }
        return $this->destination;
    }


    /**
     * Sets {@link self::$destination}.
     *
     * @param string|null $destination
     *
     * @return void
     */
    public function setDestination(?string $destination = null): void
    {
        $this->destination = $destination;
    }


    /**
     * Fetches {@link self::$desc}.
     *
     * @return string|null
     */
    #[Pure] public function getDesc(): ?string
    {
        return $this->desc;
    }


    /**
     * Sets {@link self::$desc}.
     *
     * @param string|null $desc
     *
     * @return void
     */
    public function setDesc(?string $desc = null): void
    {
        $this->desc = $desc;
    }


    /**
     * Fetches {@link self::$amount}.
     *
     * @return float
     */
    #[Pure] public function getAmount(): float
    {
        return $this->amount;
    }


    /**
     * Sets {@link self::$amount}.
     *
     * @param float $amount
     *
     * @return void
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }


    /**
     * Fetches {@link self::$status}.
     *
     * @return TransactionStatus
     */
    #[Pure] public function getStatus(): TransactionStatus
    {
        return $this->status;
    }


    /**
     * Sets {@link self::$status}.
     *
     * @param TransactionStatus $status
     *
     * @return void
     */
    public function setStatus(TransactionStatus $status): void
    {
        $this->status = $status;
    }
}
