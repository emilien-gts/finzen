<?php

namespace App\Tests\Transaction;

use App\Entity\Transaction;
use App\Factory\AccountFactory;
use App\Tests\BaseApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountTransactionTest extends BaseApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testGetTransactionsByAccount(): void
    {
        $account = AccountFactory::createOne();

        $client = static::createClient();
        $response = $client->request(Request::METHOD_GET, '/accounts/'.$account->id.'/transactions');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Vérification du corps JSON-LD de la sous-collection
        $this->assertJsonContains([
            '@context' => '/contexts/Transaction',
            '@id' => '/accounts/'.$account->id.'/transactions',
            '@type' => 'Collection',
            'totalItems' => 100,
        ]);

        $data = $response->toArray();
        $this->assertArrayHasKey('member', $data);
        $this->assertCount(30, $data['member']); // Pagination par défaut

        foreach ($data['member'] as $item) {
            $this->assertStringStartsWith('/transactions/', $item['@id']);
            $this->assertSame('Transaction', $item['@type']);
            $this->assertArrayHasKey('date', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('details', $item);
            $this->assertArrayHasKey('amount', $item);
            $this->assertSame('/accounts/'.$account->id, $item['account']);
        }

        $this->assertArrayHasKey('view', $data);
        $this->assertSame('/accounts/'.$account->id.'/transactions?page=1', $data['view']['@id']);

        $this->assertMatchesResourceCollectionJsonSchema(Transaction::class);
    }
}
