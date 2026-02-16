<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class CleanupMarketCard99Data extends Command
{
    protected $signature = 'marketcard99:cleanup 
        {--execute : Apply changes to database}
        {--reject-open : Reject open MarketCard99 orders before cleanup}';

    protected $description = 'Safely cleanup MarketCard99 catalog data and optionally reject open orders.';

    public function handle(OrderStatusService $orderStatusService): int
    {
        $execute = (bool) $this->option('execute');
        $rejectOpen = (bool) $this->option('reject-open');
        $source = Service::SOURCE_MARKETCARD99;

        $before = $this->collectSnapshot($source);

        $this->line('MarketCard99 cleanup snapshot (before):');
        foreach ($before as $key => $value) {
            $this->line("- {$key}: {$value}");
        }

        if (! $execute) {
            $this->warn('Dry run only. Re-run with --execute to apply changes.');
            return self::SUCCESS;
        }

        $result = [
            'orders_rejected' => 0,
            'orders_reject_failed' => 0,
            'services_deleted' => 0,
            'services_sanitized' => 0,
            'categories_deactivated' => 0,
            'categories_deleted' => 0,
        ];

        try {
            if ($rejectOpen) {
                $actor = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
                    ?? User::query()->first();

                if (! $actor) {
                    $this->error('No user found to act as order updater. Open orders were not rejected.');
                } else {
                    $openOrders = Order::query()
                        ->whereIn('status', [
                            Order::STATUS_NEW,
                            Order::STATUS_PROCESSING,
                            Order::STATUS_CREATING_EXTERNAL,
                            Order::STATUS_SUBMITTED,
                            Order::STATUS_FULFILLED,
                            Order::STATUS_FAILED,
                            Order::STATUS_REFUNDED,
                        ])
                        ->whereHas('service', fn ($q) => $q->where('source', $source))
                        ->get();

                    foreach ($openOrders as $order) {
                        try {
                            $orderStatusService->updateStatus(
                                $order,
                                Order::STATUS_REJECTED,
                                'تم إيقاف مزود MarketCard99 وتنظيف البيانات المرتبطة به.',
                                $actor
                            );
                            $result['orders_rejected']++;
                        } catch (Throwable) {
                            $result['orders_reject_failed']++;
                        }
                    }
                }
            }

            DB::transaction(function () use (&$result, $source): void {
                $result['categories_deactivated'] = Category::query()
                    ->where('source', $source)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                $servicesToDelete = Service::query()
                    ->where('source', $source)
                    ->doesntHave('orders')
                    ->pluck('id')
                    ->all();

                if (! empty($servicesToDelete)) {
                    $result['services_deleted'] = Service::query()
                        ->whereIn('id', $servicesToDelete)
                        ->delete();
                }

                $result['services_sanitized'] = Service::query()
                    ->where('source', $source)
                    ->update([
                        'is_active' => false,
                        'external_product_id' => null,
                        'external_type' => null,
                        'provider_payload' => null,
                        'provider_price' => null,
                        'provider_unit_price' => null,
                        'provider_is_available' => false,
                        'provider_last_synced_at' => null,
                        'requires_customer_id' => false,
                        'requires_amount' => false,
                        'supports_purchase_password' => false,
                        'requires_purchase_password' => false,
                        'sync_rule_mode' => Service::SYNC_RULE_MANUAL,
                    ]);

                while (true) {
                    $categoryIds = Category::query()
                        ->where('source', $source)
                        ->doesntHave('services')
                        ->doesntHave('children')
                        ->pluck('id')
                        ->all();

                    if (empty($categoryIds)) {
                        break;
                    }

                    $result['categories_deleted'] += Category::query()
                        ->whereIn('id', $categoryIds)
                        ->delete();
                }
            });
        } catch (Throwable $e) {
            $this->error('Cleanup failed: '.$e->getMessage());
            return self::FAILURE;
        }

        $after = $this->collectSnapshot($source);

        $this->newLine();
        $this->info('Cleanup applied successfully.');
        $this->line('Changes:');
        foreach ($result as $key => $value) {
            $this->line("- {$key}: {$value}");
        }

        $this->newLine();
        $this->line('MarketCard99 cleanup snapshot (after):');
        foreach ($after as $key => $value) {
            $this->line("- {$key}: {$value}");
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string,int>
     */
    private function collectSnapshot(string $source): array
    {
        return [
            'categories_total' => Category::query()->where('source', $source)->count(),
            'categories_active' => Category::query()->where('source', $source)->where('is_active', true)->count(),
            'services_total' => Service::query()->where('source', $source)->count(),
            'services_active' => Service::query()->where('source', $source)->where('is_active', true)->count(),
            'services_with_orders' => Service::query()->where('source', $source)->has('orders')->count(),
            'orders_open' => Order::query()
                ->whereIn('status', [
                    Order::STATUS_NEW,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_CREATING_EXTERNAL,
                    Order::STATUS_SUBMITTED,
                    Order::STATUS_FULFILLED,
                    Order::STATUS_FAILED,
                    Order::STATUS_REFUNDED,
                ])
                ->whereHas('service', fn ($q) => $q->where('source', $source))
                ->count(),
            'orders_total' => Order::query()
                ->whereHas('service', fn ($q) => $q->where('source', $source))
                ->count(),
        ];
    }
}

