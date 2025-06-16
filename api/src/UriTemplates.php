<?php

namespace App;

class UriTemplates
{
    // transactions
    public const string ACCOUNT_TRANSACTIONS = '/accounts/{id}/transactions';
    public const string ACCOUNT_BALANCE = '/accounts/{id}/balance';

    // errors
    public const string ERROR_BAD_REQUEST = '/errors/400';
}
