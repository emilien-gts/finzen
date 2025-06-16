<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

abstract class BaseProcessor implements ProcessorInterface
{
    #[Required]
    public EntityManagerInterface $em;

    abstract public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object;

    public function throwNotFoundIfNull(mixed $value, string $message = 'Resource not found'): void
    {
        if (null === $value) {
            throw new NotFoundHttpException($message);
        }
    }
}
