<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Services\MarketCard99Client;
use App\Services\MarketCard99OrderService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketCard99OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Service $service;
    private Wallet $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->wallet = Wallet::create(['user_id' => $this->user->id, 'balance' => 1000, 'held_balance' => 0]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Test Service',
            'slug' => 'test-service',
            'price' => 100,
            'is_active' => true,
            'source' => 'marketcard99',
            'external_product_id' => 123,
            'external_type' => 'id',
            'requires_customer_id' => true,
            'requires_amount' => false,
        ]);
    }

    public function test_order_creation_holds_wallet_balance()
    {
        $initialBalance = $this->wallet->balance;

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('createBill')->andReturn(['ok' => true]);
        $client->shouldReceive('resolveBillIdAfterCreate')->andReturn([
            'external_bill_id' => 999,
            'external_uuid' => 'test-uuid',
            'external_status' => 'pending',
            'external_raw' => [],
        ]);

        $orderService = app(MarketCard99OrderService::class);
        $order = $orderService->createOrder($this->user, $this->service, [
            'selected_price' => 100,
            'customer_identifier' => 'player123',
        ]);

        $this->wallet->refresh();

        $this->assertEquals($initialBalance - 100, $this->wallet->balance);
        $this->assertEquals(100, $this->wallet->held_balance);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'status' => 'processing',
            'amount_held' => 100,
        ]);
    }

    public function test_external_create_failure_triggers_refund()
    {
        $initialBalance = $this->wallet->balance;

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('createBill')->andReturn([
            'ok' => false,
            'error_message' => 'API Error',
        ]);

        $orderService = app(MarketCard99OrderService::class);
        $order = $orderService->createOrder($this->user, $this->service, [
            'selected_price' => 100,
            'customer_identifier' => 'player123',
        ]);

        $this->wallet->refresh();
        $order->refresh();

        $this->assertEquals($initialBalance, $this->wallet->balance);
        $this->assertEquals(0, $this->wallet->held_balance);
        $this->assertSame('rejected', $order->status);
    }

    public function test_status_sync_updates_to_done()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'status' => 'processing',
            'external_bill_id' => 999,
            'price_at_purchase' => 100,
            'amount_held' => 100,
        ]);

        app(WalletService::class)->holdAmount($this->wallet, '100', [
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('getBill')->andReturn([
            'ok' => true,
            'data' => [
                'data' => [
                    'bill' => [
                        'id' => 999,
                        'status' => 'success',
                    ],
                ],
            ],
        ]);

        $orderService = app(MarketCard99OrderService::class);
        $orderService->syncOrderStatus($order, $this->user);

        $order->refresh();
        $this->wallet->refresh();

        $this->assertEquals('done', $order->status);
        $this->assertEquals('success', $order->external_status);
        $this->assertEquals(900, $this->wallet->balance);
        $this->assertEquals(0, $this->wallet->held_balance);
    }

    public function test_status_sync_sees_cancel_and_releases_once()
    {
        $initialBalance = $this->wallet->balance;

        $order = Order::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'status' => 'processing',
            'external_bill_id' => 999,
            'price_at_purchase' => 100,
            'amount_held' => 100,
        ]);

        app(WalletService::class)->holdAmount($this->wallet, '100', [
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);

        $this->wallet->refresh();
        $this->assertEquals($initialBalance - 100, $this->wallet->balance);

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('getBill')->andReturn([
            'ok' => true,
            'data' => [
                'data' => [
                    'bill' => [
                        'id' => 999,
                        'status' => 'cancel',
                    ],
                ],
            ],
        ]);

        $orderService = app(MarketCard99OrderService::class);
        $orderService->syncOrderStatus($order, $this->user);

        $order->refresh();
        $this->wallet->refresh();

        $this->assertEquals('rejected', $order->status);
        $this->assertEquals($initialBalance, $this->wallet->balance);

        $orderService->syncOrderStatus($order, $this->user);
        $this->wallet->refresh();
        $this->assertEquals($initialBalance, $this->wallet->balance);
    }
}
