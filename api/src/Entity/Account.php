<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Exception\CannotDeleteAccountWithTransactionsException;
use App\State\AccountDeleteProcessor;
use App\Traits\IdTrait;
use App\UriTemplates;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(operations: [new GetCollection(), new Post(), new Get(), new Patch()])]
#[Delete(
    errors: [CannotDeleteAccountWithTransactionsException::class],
    processor: AccountDeleteProcessor::class
)]
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

    public function canBeDeleted(): bool
    {
        return $this->transactions->isEmpty();
    }
}
