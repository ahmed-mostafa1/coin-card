<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;

class OpsOrderController extends Controller
{
    public function startProcessing(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): RedirectResponse
    {
        $orderStatusService->updateStatus($order, $request->input('status'), $request->input('admin_note'), $request->user());

        return redirect()->route('admin.ops.index', ['tab' => 'orders_new'])
            ->with('status', 'تم تحويل الطلب إلى قيد التنفيذ.');
    }

    public function markDone(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): RedirectResponse
    {
        $orderStatusService->updateStatus($order, $request->input('status'), $request->input('admin_note'), $request->user());

        return redirect()->route('admin.ops.index', ['tab' => 'orders_processing'])
            ->with('status', 'تم اعتماد الطلب بنجاح.');
    }

    public function reject(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): RedirectResponse
    {
        $orderStatusService->updateStatus($order, $request->input('status'), $request->input('admin_note'), $request->user());

        return redirect()->route('admin.ops.index', ['tab' => 'orders_processing'])
            ->with('status', 'تم رفض الطلب.');
    }
}
