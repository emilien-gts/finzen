<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Traits\IdTrait;
use App\UriTemplates;
use App\Validator\Constraints\Amount;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource]
#[ApiResource(
    uriTemplate: UriTemplates::ACCOUNT_TRANSACTIONS,
    operations: [new GetCollection()],
    uriVariables: [
        'id' => new Link(toProperty: 'account', fromClass: Account::class),
    ],
)]
class Transaction
{
    use IdTrait;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    public ?\DateTime $date = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotEqualTo('')]
    public ?string $details = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\Type(Types::STRING)]
    #[Amount]
    public ?string $amount = null;

    #[ORM\ManyToOne(targetEntity: Account::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    #[Assert\NotNull]
    public ?Account $account = null;
}
