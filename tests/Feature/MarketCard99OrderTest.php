<?php

namespace Tests\Feature;

use App\Models\Order;
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
        
        $this->service = Service::create([
            'name' => 'Test Service',
            'slug' => 'test-service',
            'price' => 100,
            'is_active' => true,
            'external_product_id' => 123,
            'external_type' => 'id',
            'requires_customer_id' => true,
            'requires_amount' => false,
        ]);
    }

    public function test_order_creation_deducts_wallet()
    {
        $initialBalance = $this->wallet->balance;

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('createBill')->andReturn(['success' => true]);
        $client->shouldReceive('resolveBillIdAfterCreate')->andReturn([
            'external_bill_id' => 999,
            'external_uuid' => 'test-uuid',
            'external_status' => 'pending',
            'external_raw' => [],
        ]);

        $orderService = app(MarketCard99OrderService::class);
        
        $order = $orderService->createOrder(
            $this->user,
            $this->service,
            1,
            'player123'
        );

        $this->wallet->refresh();

        $this->assertEquals($initialBalance - 100, $this->wallet->balance);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'sell_total' => 100,
        ]);
    }

    public function test_external_create_failure_triggers_refund()
    {
        $initialBalance = $this->wallet->balance;

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('createBill')->andReturn([
            'success' => false,
            'message' => 'API Error',
        ]);

        $orderService = app(MarketCard99OrderService::class);

        try {
            $orderService->createOrder(
                $this->user,
                $this->service,
                1,
                'player123'
            );
        } catch (\Exception $e) {
            // Expected to throw
        }

        $this->wallet->refresh();

        // Balance should be refunded
        $this->assertEquals($initialBalance, $this->wallet->balance);
    }

    public function test_status_sync_updates_to_fulfilled()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'qty' => 1,
            'sell_unit_price' => 100,
            'sell_total' => 100,
            'status' => 'submitted',
            'external_bill_id' => 999,
            'price_at_purchase' => 100,
        ]);

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('getBill')->andReturn([
            'id' => 999,
            'status' => 'success',
        ]);

        $orderService = app(MarketCard99OrderService::class);
        $orderService->syncOrderStatus($order);

        $order->refresh();

        $this->assertEquals('fulfilled', $order->status);
        $this->assertEquals('success', $order->external_status);
    }

    public function test_status_sync_sees_cancel_and_refunds_once()
    {
        $initialBalance = $this->wallet->balance;

        $order = Order::create([
            'user_id' => $this->user->id,
            'service_id' => $this->service->id,
            'qty' => 1,
            'sell_unit_price' => 100,
            'sell_total' => 100,
            'status' => 'submitted',
            'external_bill_id' => 999,
            'price_at_purchase' => 100,
        ]);

        // Deduct balance first
        app(WalletService::class)->debit($this->wallet, '100', [
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);

        $this->wallet->refresh();
        $this->assertEquals($initialBalance - 100, $this->wallet->balance);

        $client = $this->mock(MarketCard99Client::class);
        $client->shouldReceive('getBill')->andReturn([
            'id' => 999,
            'status' => 'cancel',
        ]);

        $orderService = app(MarketCard99OrderService::class);
        $orderService->syncOrderStatus($order);

        $order->refresh();
        $this->wallet->refresh();

        $this->assertEquals('refunded', $order->status);
        $this->assertEquals($initialBalance, $this->wallet->balance);

        // Sync again - should not refund twice
        $orderService->syncOrderStatus($order);
        $this->wallet->refresh();
        $this->assertEquals($initialBalance, $this->wallet->balance);
    }
}
