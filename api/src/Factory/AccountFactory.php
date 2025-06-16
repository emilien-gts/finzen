<?php

namespace App\Factory;

use App\Entity\Account;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Account>
 */
final class AccountFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Account::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->text(),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Account $account): void {
            TransactionFactory::createMany(100, ['account' => $account]);
        });
    }
}
