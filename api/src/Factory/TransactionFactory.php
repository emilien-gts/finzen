<?php

namespace App\Factory;

use App\Entity\Transaction;
use App\Utils\ZendstruckUtils;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Transaction>
 */
final class TransactionFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Transaction::class;
    }

    protected function defaults(): array
    {
        return [
            'date' => self::faker()->dateTimeBetween('-12 months', 'now'),
            'label' => self::faker()->text(25),
            'details' => self::faker()->text(),
            'amount' => ZendstruckUtils::generateAmount(-9999, 9999),
        ];
    }
}
