<?php

namespace App\Exception;

use ApiPlatform\Metadata\ErrorResource;
use ApiPlatform\State\ApiResource\Error;
use App\UriTemplates;

#[ErrorResource]
class CannotDeleteAccountWithTransactionsException extends Error
{
    public const string TITLE = 'Account deletion forbidden';
    public const string DETAIL = 'Cannot delete an account with transactions';

    public function __construct()
    {
        parent::__construct(
            title: self::TITLE,
            detail: self::DETAIL,
            status: 400,
            type: UriTemplates::ERROR_BAD_REQUEST,
        );
    }
}
