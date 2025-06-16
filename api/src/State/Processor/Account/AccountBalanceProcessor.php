<?php

namespace App\State\Processor\Account;

use ApiPlatform\Metadata\Operation;
use App\Entity\Account;
use App\Model\AccountBalanceDto;
use App\Service\AccountService;
use App\State\Processor\BaseProcessor;

class AccountBalanceProcessor extends BaseProcessor
{
    public function __construct(
        private readonly AccountService $service,
    ) {
    }

    /**
     * @param AccountBalanceDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Account
    {
        $account = $this->em->getRepository(Account::class)->find($uriVariables['id']);
        $this->throwNotFoundIfNull($account);

        $this->service->updateBalance($account, $data->balance);

        return $account;
    }
}
