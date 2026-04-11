<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ApiProviderManager;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiProviderOrderSyncController extends Controller
{
    public function __construct(
        private readonly ApiProviderManager $providerManager,
        private readonly OrderStatusService $orderStatusService
    ) {}

    public function syncAllProcessingStatus(Request $request): RedirectResponse
    {
        $orders = Order::where('status', 'processing')
                       ->whereNotNull('provider_transaction_id')
                       ->with('service')
                       ->get();

        $totalChecked = 0;
        $totalChanged = 0;
        $totalFailed = 0;

        foreach ($orders as $order) {
            $orderService = $this->providerManager->forOrder($order);
            if (! $orderService) {
                continue;
            }

            $totalChecked++;
            
            try {
                $sync = $orderService->syncStatus($order);
                if (! ($sync['ok'] ?? false)) {
                    $totalFailed++;
                    continue;
                }

                $localStatus = $orderService->mapToLocalStatus($sync['execution_status'] ?? '');

                if ($localStatus && ! in_array($order->status, [Order::STATUS_DONE, Order::STATUS_REJECTED], true)) {
                    $this->orderStatusService->updateStatus(
                        $order,
                        $localStatus,
                        'تم التحديث التلقائي الشامل من المزود: ' . ($sync['execution_status'] ?? ''),
                        $request->user()
                    );
                }

                if ($sync['changed'] ?? false) {
                    $totalChanged++;
                }

            } catch (\Throwable $e) {
                $totalFailed++;
                \Illuminate\Support\Facades\Log::error('Bulk Sync Failed for order ' . $order->id, ['error' => $e->getMessage()]);
            }
        }

        return back()->with('success', "تم فحص {$totalChecked} طلب(ات). تغيرت حالة {$totalChanged}، وفشل {$totalFailed}.");
    }

    public function syncStatus(Order $order, Request $request): RedirectResponse
    {
        $orderService = $this->providerManager->forOrder($order);

        if (! $orderService) {
            return back()->with('error', 'هذا الطلب لا ينتمي لمزود خارجي أو المزود غير مفعّل.');
        }

        $sync = $orderService->syncStatus($order);

        if (! ($sync['ok'] ?? false)) {
            return back()->with('error', 'فشل تحديث الحالة: ' . ($sync['error_message'] ?? 'خطأ غير معروف'));
        }

        $localStatus = $orderService->mapToLocalStatus($sync['execution_status'] ?? '');

        if ($localStatus && ! in_array($order->status, [Order::STATUS_DONE, Order::STATUS_REJECTED], true)) {
            try {
                $this->orderStatusService->updateStatus(
                    $order,
                    $localStatus,
                    'تم التحديث تلقائياً من المزود: ' . ($sync['execution_status'] ?? ''),
                    $request->user()
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'جُلبت الحالة لكن فشل تحديث الطلب: ' . $e->getMessage());
            }
        }

        $message = $sync['changed']
            ? 'تم تحديث حالة الطلب من المزود بنجاح.'
            : 'لا يوجد تغيير في الحالة من المزود.';

        return back()->with('success', $message);
    }
}
