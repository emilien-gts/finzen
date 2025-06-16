<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ApiTransaction extends ApiResource
{
    public function __construct()
    {
        parent::__construct(
            operations: [
                new GetCollection(),
                new Get(),
            ]
        );
    }
}
