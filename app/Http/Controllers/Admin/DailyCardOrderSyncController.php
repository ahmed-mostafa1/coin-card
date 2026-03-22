<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\DailyCardOrderService;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DailyCardOrderSyncController extends Controller
{
    public function __construct(
        private readonly DailyCardOrderService $dailyCardOrderService,
        private readonly OrderStatusService $orderStatusService
    ) {}

    public function syncStatus(Order $order, Request $request): RedirectResponse
    {
        if (! $order->service || $order->service->source !== Service::SOURCE_DAILYCARD) {
            return back()->with('error', 'هذا الطلب لا ينتمي لمزود DailyCard.');
        }

        $sync = $this->dailyCardOrderService->syncStatus($order);

        if (! ($sync['ok'] ?? false)) {
            return back()->with('error', 'فشل تحديث الحالة: ' . ($sync['error_message'] ?? 'خطأ غير معروف'));
        }

        $localStatus = $this->dailyCardOrderService->mapToLocalStatus(
            $sync['execution_status'] ?? '',
            $sync['provider_status'] ?? ''
        );

        if ($localStatus && ! in_array($order->status, [Order::STATUS_DONE, Order::STATUS_REJECTED], true)) {
            try {
                $this->orderStatusService->updateStatus(
                    $order,
                    $localStatus,
                    'تم التحديث تلقائياً من DailyCard: ' . ($sync['execution_status'] ?? ''),
                    $request->user()
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'جُلبت الحالة لكن فشل تحديث الطلب: ' . $e->getMessage());
            }
        }

        $message = $sync['changed']
            ? 'تم تحديث حالة الطلب من DailyCard بنجاح.'
            : 'لا يوجد تغيير في الحالة من DailyCard.';

        return back()->with('success', $message);
    }
}
