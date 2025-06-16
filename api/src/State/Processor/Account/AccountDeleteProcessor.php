<?php

namespace App\State\Processor\Account;

use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\CannotDeleteAccountWithTransactionsException;
use App\State\Processor\BaseProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AccountDeleteProcessor extends BaseProcessor
{
    public function __construct(
        #[Autowire(service: RemoveProcessor::class)]
        private ProcessorInterface $removeProcessor,
    ) {
    }

    /**
     * @throws CannotDeleteAccountWithTransactionsException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        if (!$data->canBeDeleted()) {
            throw new CannotDeleteAccountWithTransactionsException();
        }

        return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }
}
