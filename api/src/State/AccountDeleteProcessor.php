<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Account;
use App\Exception\CannotDeleteAccountWithTransactionsException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class AccountDeleteProcessor implements ProcessorInterface
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
        if (!$operation instanceof DeleteOperationInterface) {
            throw new \LogicException('Expecting an DeleteOperationInterface, got '.get_class($operation));
        }

        if (!$data instanceof Account) {
            throw new \LogicException('Expecting an Account, got '.get_class($data));
        }

        if (!$data->canBeDeleted()) {
            throw new CannotDeleteAccountWithTransactionsException();
        }

        return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }
}
