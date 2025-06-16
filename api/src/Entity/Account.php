<?php

namespace App\Entity;

use App\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use App\Traits\IdTrait;
use App\UriTemplates;
use App\Utils\MathUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource\ApiAccount]
class Account
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public ?string $name = null;

    /** @var ArrayCollection<int, Transaction> */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'account', cascade: ['all'])]
    #[ApiProperty(uriTemplate: UriTemplates::ACCOUNT_TRANSACTIONS)]
    public Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions->add($transaction);
        $transaction->account = $this;
    }

    public function removeTransaction(Transaction $transaction): void
    {
        $this->transactions->removeElement($transaction);
        $transaction->account = null;
    }

    public function getBalance(): string
    {
        return $this->transactions->reduce(static fn (string $carry, Transaction $t) => MathUtils::add($carry, $t->amount), '0.00');
    }

    public function canBeDeleted(): bool
    {
        return $this->transactions->isEmpty();
    }
}
