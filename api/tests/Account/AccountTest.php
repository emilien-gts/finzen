<?php

namespace App\Tests\Account;

use App\Exception\CannotDeleteAccountWithTransactionsException;
use App\Factory\AccountFactory;
use App\Tests\BaseApiTestCase;
use App\UriTemplates;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountTest extends BaseApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testGetCollection(): void
    {
        $account = AccountFactory::createOne();

        $client = static::createClient();
        $response = $client->request(Request::METHOD_GET, '/accounts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Account',
            '@id' => '/accounts',
            '@type' => 'Collection',
            'totalItems' => 1,
        ]);

        $data = $response->toArray();
        $this->assertArrayHasKey('member', $data);
        $this->assertCount(1, $data['member']);

        $this->assertSame('/accounts/'.$account->id, $data['member'][0]['@id']);
        $this->assertSame('Account', $data['member'][0]['@type']);
        $this->assertSame($account->name, $data['member'][0]['name']);
        $this->assertSame('/accounts/'.$account->id.'/transactions', $data['member'][0]['transactions']);
        $this->assertSame($account->id, $data['member'][0]['id']);
    }

    public function testCreateAccount(): void
    {
        $response = static::createClient()->request(Request::METHOD_POST, '/accounts', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => ['name' => 'Compte principal'],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Account',
            '@type' => 'Account',
            'name' => 'Compte principal',
        ]);

        $this->assertJsonLdResource($response, 'Account');
    }

    public function testCreateInvalidAccount(): void
    {
        static::createClient()->request(Request::METHOD_POST, '/accounts', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => ['name' => ''], // Violates NotBlank
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertJsonContains([
            '@context' => '/contexts/ConstraintViolation',
            '@type' => 'ConstraintViolation',
            'status' => 422,
        ]);
    }

    public function testUpdateAccount(): void
    {
        $account = AccountFactory::createOne();

        static::createClient()->request(Request::METHOD_PATCH, '/accounts/'.$account->id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => 'Nouveau nom de compte'],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Account',
            '@type' => 'Account',
            '@id' => '/accounts/'.$account->id,
            'name' => 'Nouveau nom de compte',
        ]);
    }

    public function testDeleteAccountWithoutTransactions(): void
    {
        $account = AccountFactory::createOne();

        $em = static::getContainer()->get('doctrine')->getManager();

        foreach ($account->transactions as $transaction) {
            $transaction->account = null; // supprime la relation
            $em->remove($transaction);   // supprime l'entité elle-même
        }

        $em->flush(); // applique les suppressions en base

        static::createClient()->request(Request::METHOD_DELETE, '/accounts/'.$account->id);
        $this->assertResponseStatusCodeSame(204);

        static::createClient()->request(Request::METHOD_GET, '/accounts/'.$account->id);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteAccountWithTransactions(): void
    {
        $account = AccountFactory::createOne();

        static::createClient()->request(Request::METHOD_DELETE, '/accounts/'.$account->id);
        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            '@context' => '/contexts/Error',
            '@id' => UriTemplates::ERROR_BAD_REQUEST,
            '@type' => 'Error',
            'id' => '400',
            'title' => CannotDeleteAccountWithTransactionsException::TITLE,
            'detail' => CannotDeleteAccountWithTransactionsException::DETAIL,
            'status' => 400,
            'type' => UriTemplates::ERROR_BAD_REQUEST,
            'description' => CannotDeleteAccountWithTransactionsException::DETAIL,
        ]);
    }
}
