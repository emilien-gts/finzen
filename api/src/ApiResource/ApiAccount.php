<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Exception\CannotDeleteAccountWithTransactionsException;
use App\Model\AccountBalanceDto;
use App\State\Processor\Account\AccountBalanceProcessor;
use App\State\Processor\Account\AccountDeleteProcessor;
use App\UriTemplates;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ApiAccount extends ApiResource
{
    public function __construct()
    {
        parent::__construct(
            operations: [
                new GetCollection(),
                new Get(),
                new Post(),
                new Patch(),
                new Delete(
                    errors: [CannotDeleteAccountWithTransactionsException::class],
                    processor: AccountDeleteProcessor::class
                ),
                // balance
                new Patch(
                    uriTemplate: UriTemplates::ACCOUNT_BALANCE,
                    openapi: self::accountBalanceOpenApi(),
                    input: AccountBalanceDto::class,
                    processor: AccountBalanceProcessor::class
                ),
            ]
        );
    }

    private static function accountBalanceOpenApi(): Model\Operation
    {
        return new Model\Operation(
            summary: 'Update account balance',
        );
    }
}
