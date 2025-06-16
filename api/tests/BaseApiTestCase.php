<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class BaseApiTestCase extends ApiTestCase
{
    protected function isoDate(\DateTimeInterface $date): string
    {
        return $date->format(\DateTimeInterface::ATOM);
    }

    protected function iriFrom(string $class, array $criteria): string
    {
        return $this->findIriBy($class, $criteria);
    }

    protected function assertJsonLdResource(ResponseInterface $response, string $type, ?string $iri = null): void
    {
        $data = $response->toArray();

        $this->assertArrayHasKey('@type', $data);
        $this->assertSame($type, $data['@type']);

        if (null !== $iri) {
            $this->assertSame($iri, $data['@id']);
        }
    }
}
