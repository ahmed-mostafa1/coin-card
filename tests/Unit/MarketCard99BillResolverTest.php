<?php

namespace Tests\Unit;

use App\Services\MarketCard99Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketCard99BillResolverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.marketcard99.base_url', 'https://app.market-card99.com');
        config()->set('services.marketcard99.enabled', true);
        config()->set('services.marketcard99.token', 'testing-token');
    }

    public function test_bill_resolver_filters_by_product_id()
    {
        $client = new MarketCard99Client();

        Http::fake([
            '*/api/v2/bills' => Http::response([
                [
                    'id' => 100,
                    'product' => ['id' => 123],
                    'customer_id' => 'player123',
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
                [
                    'id' => 101,
                    'product' => ['id' => 456], // Different product
                    'customer_id' => 'player123',
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
            ], 200),
        ]);

        $result = $client->resolveBillIdAfterCreate(123, 'player123', Carbon::now());

        $this->assertNotNull($result);
        $this->assertEquals(100, $result['external_bill_id']);
    }

    public function test_bill_resolver_filters_by_customer_id()
    {
        $client = new MarketCard99Client();

        Http::fake([
            '*/api/v2/bills' => Http::response([
                [
                    'id' => 100,
                    'product' => ['id' => 123],
                    'customer_id' => 'player123',
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
                [
                    'id' => 101,
                    'product' => ['id' => 123],
                    'customer_id' => 'player456', // Different customer
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
            ], 200),
        ]);

        $result = $client->resolveBillIdAfterCreate(123, 'player123', Carbon::now());

        $this->assertNotNull($result);
        $this->assertEquals(100, $result['external_bill_id']);
    }

    public function test_bill_resolver_sorts_by_id_desc()
    {
        $client = new MarketCard99Client();

        Http::fake([
            '*/api/v2/bills' => Http::response([
                [
                    'id' => 100,
                    'product' => ['id' => 123],
                    'customer_id' => 'player123',
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
                [
                    'id' => 102, // Newer ID
                    'product' => ['id' => 123],
                    'customer_id' => 'player123',
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
            ], 200),
        ]);

        $result = $client->resolveBillIdAfterCreate(123, 'player123', Carbon::now());

        $this->assertNotNull($result);
        $this->assertEquals(102, $result['external_bill_id']);
    }

    public function test_bill_resolver_returns_null_when_no_match()
    {
        $client = new MarketCard99Client();

        Http::fake([
            '*/api/v2/bills' => Http::response([
                [
                    'id' => 100,
                    'product' => ['id' => 456], // Different product
                    'customer_id' => 'player123',
                    'created_at' => now()->format('Y-m-d h:i a'),
                ],
            ], 200),
        ]);

        $result = $client->resolveBillIdAfterCreate(123, 'player123', Carbon::now());

        $this->assertNull($result);
    }
}
