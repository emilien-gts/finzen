<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Account;
use App\Utils\MathUtils;
use Doctrine\ORM\EntityManagerInterface;

readonly class AccountService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function updateBalance(Account $account, string $balance): void
    {
        $current = $account->getBalance();
        $diff = MathUtils::sub($balance, $current);

        if (MathUtils::isEqualToZero($diff)) {
            return;
        }

        $transaction = new Transaction();
        $transaction->date = new \DateTime();
        $transaction->amount = $diff;
        $transaction->label = 'adjustment';

        $account->addTransaction($transaction);
        $this->save($account);
    }

    public function save(Account $account): void
    {
        $this->em->persist($account);
        $this->em->flush();
    }
}
