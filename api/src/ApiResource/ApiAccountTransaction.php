<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Account;
use App\UriTemplates;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ApiAccountTransaction extends ApiResource
{
    public function __construct()
    {
        parent::__construct(
            uriTemplate: UriTemplates::ACCOUNT_TRANSACTIONS,
            operations: [
                new GetCollection(),
            ],
            uriVariables: [
                'id' => new Link(toProperty: 'account', fromClass: Account::class),
            ],
            order: ['date' => 'DESC', 'id' => 'DESC']
        );
    }
}
