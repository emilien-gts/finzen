<?php

namespace App\Tests\Transaction;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Factory\AccountFactory;
use App\Factory\TransactionFactory;
use App\Tests\BaseApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TransactionTest extends BaseApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testGetCollection(): void
    {
        AccountFactory::createOne();

        $client = static::createClient();
        $response = $client->request(Request::METHOD_GET, '/transactions');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Transaction',
            '@id' => '/transactions',
            '@type' => 'Collection',
        ]);

        $data = $response->toArray();
        $this->assertArrayHasKey('member', $data);
        $this->assertCount(30, $data['member']);

        $this->assertMatchesResourceCollectionJsonSchema(Transaction::class);
    }

    public function testCreateTransaction(): void
    {
        $account = AccountFactory::createOne();
        $date = new \DateTime('2023-05-01', new \DateTimeZone('UTC'));

        static::createClient()->request(Request::METHOD_POST, '/transactions', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'date' => $this->isoDate($date),
                'label' => 'Achat matériel',
                'details' => 'Ordinateur portable',
                'amount' => '789.45',
                'account' => $this->iriFrom(Account::class, ['id' => $account->id]),
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Transaction',
            '@type' => 'Transaction',
            'date' => $this->isoDate($date),
            'label' => 'Achat matériel',
            'details' => 'Ordinateur portable',
            'amount' => '789.45',
            'account' => $this->iriFrom(Account::class, ['id' => $account->id]),
        ]);

        $this->assertMatchesResourceItemJsonSchema(Transaction::class);
    }

    public function testCreateInvalidTransaction(): void
    {
        static::createClient()->request(Request::METHOD_POST, '/transactions', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'label' => '',
                'details' => '',
                'amount' => 'invalid',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertJsonContains([
            '@context' => '/contexts/ConstraintViolation',
            '@type' => 'ConstraintViolation',
            'status' => 422,
        ]);
    }

    public function testUpdateTransaction(): void
    {
        $date = new \DateTime('2023-05-01', new \DateTimeZone('UTC'));
        $account = AccountFactory::createOne();

        $transaction = TransactionFactory::createOne([
            'date' => $date,
            'account' => $account,
        ]);

        static::createClient()->request(Request::METHOD_PATCH, '/transactions/'.$transaction->id, [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'label' => 'Padel',
                'details' => 'Urban Soccer',
                'amount' => '8.00',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Transaction',
            '@type' => 'Transaction',
            'date' => $this->isoDate($date),
            'label' => 'Padel',
            'details' => 'Urban Soccer',
            'amount' => '8.00',
            'account' => $this->iriFrom(Account::class, ['id' => $account->id]),
        ]);
    }

    public function testDeleteTransaction(): void
    {
        $account = AccountFactory::createOne();
        $transaction = TransactionFactory::createOne([
            'label' => 'to delete',
            'account' => $account,
        ]);

        static::createClient()->request(Request::METHOD_DELETE, '/transactions/'.$transaction->id);
        $this->assertResponseStatusCodeSame(204);

        static::createClient()->request(Request::METHOD_GET, '/transactions/'.$transaction->id);
        $this->assertResponseStatusCodeSame(404);
    }
}
