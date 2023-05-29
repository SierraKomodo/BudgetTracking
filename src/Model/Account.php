<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use JetBrains\PhpStorm\Pure;
use SierraKomodo\BudgetTracking\Enum\AccountType;
use SierraKomodo\BudgetTracking\Enum\TransactionStatus;
use SierraKomodo\BudgetTracking\Repository\AccountRepository;

/**
 * Model for `accounts` table entries.
 */
#[Entity(repositoryClass: AccountRepository::class)]
#[Table(name: 'accounts')]
class Account extends AbstractModel
{
    /** @var string $name The account's name. */
    #[Column(type: Types::TEXT, length: 255)]
    private string $name;

    /** @var string|null $desc The account's description. */
    #[Column(name: '`desc`', type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $desc = null;

    /** @var AccountType $accountType The account's type. */
    #[Column(name: 'account_type', type: Types::TEXT, length: 255, enumType: AccountType::class)]
    private AccountType $accountType;

    /** @var Collection|Transaction[] $transactions {@link Transaction} instances with this account as {@link Transaction::$account}. */
    #[OneToMany(mappedBy: 'account', targetEntity: Transaction::class)]
    private array|Collection $transactions;

    /** @var Collection|Transaction[] $transfers {@link Transaction} instances with this account as {@link Transaction::$destAccount}. */
    #[OneToMany(mappedBy: 'destAccount', targetEntity: Transaction::class)]
    private array|Collection $transfers;


    /** @var ?AccountCredit Linked {@link AccountCredit}. */
    #[OneToOne(targetEntity: AccountCredit::class)]
    #[JoinColumn(nullable: true)]
    private ?AccountCredit $credit = null;


    // Magic Methods

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->transfers = new ArrayCollection();
    }


    #[Pure] public function __toString(): string
    {
        return $this->name;
    }


    // Public Functions

    /**
     * Calculates and returns the balance of the account for the given status category.
     *
     * @param TransactionStatus $status
     *
     * @return float
     */
    #[Pure] public function getTransactionTotal(TransactionStatus $status
    ): float {
        $result = 0;
        foreach ($this->transactions as $transaction) {
            if ($transaction->getStatus() != $status) {
                continue;
            }
            $result += $transaction->getAmount();
        }
        foreach ($this->transfers as $transfer) {
            if ($transfer->getStatus() != $status) {
                continue;
            }
            $result -= $transfer->getAmount();
        }
        return $result;
    }

    /**
     * Calculates and returns the expected balance of the account, adding together all transactions.
     *
     * @return float
     */
    #[Pure] public function getAllTransactionTotal(): float
    {
        $result = 0;
        foreach ($this->transactions as $transaction) {
            $result += $transaction->getAmount();
        }
        foreach ($this->transfers as $transfer) {
            $result -= $transfer->getAmount();
        }
        return $result;
    }


    // Getters and Setters

    /**
     * Fetches {@link self::name}.
     *
     * @return string
     */
    #[Pure] public function getName(): string
    {
        return $this->name;
    }


    /**
     * Sets {@link self::name}.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * Fetches {@link self::$accountType}.
     *
     * @return AccountType
     */
    #[Pure] public function getAccountType(): AccountType
    {
        return $this->accountType;
    }


    /**
     * Sets {@link self::$accountType}.
     *
     * @param AccountType $accountType
     *
     * @return void
     */
    public function setAccountType(AccountType $accountType): void
    {
        $this->accountType = $accountType;
    }


    /**
     * Fetches {@link self::$transactions}.
     *
     * @return Collection|Transaction[]
     */
    #[Pure] public function getTransactions(): Collection|array
    {
        return $this->transactions;
    }


    /**
     * Fetches {@link self::$transactions}.
     *
     * @return Collection|Transaction[]
     */
    #[Pure] public function getTransfers(): Collection|array
    {
        return $this->transfers;
    }


    /**
     * Fetches {@link self::$credit}.
     *
     * @return AccountCredit|null
     */
    #[Pure] public function getCredit(): ?AccountCredit
    {
        return $this->credit;
    }


    /**
     * Sets {@link self::$credit}.
     *
     * @param ?AccountCredit $credit
     *
     * @return void
     */
    public function setCredit(?AccountCredit $credit = null): void
    {
        $this->credit = $credit;
    }
}
