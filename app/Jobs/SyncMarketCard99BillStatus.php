<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Services\MarketCard99OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncMarketCard99BillStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(MarketCard99OrderService $orderService): void
    {
        Log::info('MarketCard99: Starting bill status sync job');

        $actor = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::query()->first();
        if (!$actor) {
            Log::warning('MarketCard99: Sync skipped because no actor user exists');
            return;
        }

        // Find orders that need syncing
        $orders = Order::whereNotNull('external_bill_id')
            ->whereIn('status', ['new', 'processing', 'submitted', 'creating_external'])
            ->whereHas('service', fn ($query) => $query->where('source', 'marketcard99'))
            ->get();

        $syncedCount = 0;
        $failedCount = 0;

        foreach ($orders as $order) {
            try {
                $synced = $orderService->syncOrderStatus($order, $actor);
                
                if ($synced) {
                    $syncedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                
                Log::error('MarketCard99: Failed to sync order status', [
                    'order_id' => $order->id,
                    'external_bill_id' => $order->external_bill_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('MarketCard99: Bill status sync job completed', [
            'total_orders' => $orders->count(),
            'synced' => $syncedCount,
            'failed' => $failedCount,
        ]);
    }
}
