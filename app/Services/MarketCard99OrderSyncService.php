<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MarketCard99OrderSyncService
{
    private const CACHE_SUMMARY_KEY = 'marketcard99:orders:last_summary';
    private const LOCK_KEY = 'marketcard99:orders:sync_lock';

    public function __construct(
        private readonly MarketCard99OrderService $orderService
    ) {
    }

    public function sync(User $actor): array
    {
        $lock = Cache::lock(self::LOCK_KEY, 600);

        if (!$lock->get()) {
            return [
                'ok' => false,
                'message' => 'عملية مزامنة حالات الطلبات قيد التنفيذ حالياً.',
            ];
        }

        $summary = [
            'ok' => true,
            'started_at' => now()->toDateTimeString(),
            'total_candidates' => 0,
            'synced' => 0,
            'unchanged' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $orders = Order::query()
                ->whereNotNull('external_bill_id')
                ->whereIn('status', [
                    Order::STATUS_NEW,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SUBMITTED,
                    Order::STATUS_CREATING_EXTERNAL,
                ])
                ->whereHas('service', fn ($q) => $q->where('source', 'marketcard99'))
                ->get();

            $summary['total_candidates'] = $orders->count();

            foreach ($orders as $order) {
                try {
                    $updated = $this->orderService->syncOrderStatus($order, $actor);
                    if ($updated) {
                        $summary['synced']++;
                    } else {
                        $summary['unchanged']++;
                    }
                } catch (\Throwable $e) {
                    $summary['failed']++;
                    $summary['errors'][] = "Order #{$order->id}: {$e->getMessage()}";

                    Log::error('MarketCard99: order status sync failed', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } finally {
            optional($lock)->release();
        }

        $summary['finished_at'] = now()->toDateTimeString();
        Cache::put(self::CACHE_SUMMARY_KEY, $summary, now()->addDay());

        return $summary;
    }

    public function lastSummary(): ?array
    {
        $summary = Cache::get(self::CACHE_SUMMARY_KEY);

        return is_array($summary) ? $summary : null;
    }
}
