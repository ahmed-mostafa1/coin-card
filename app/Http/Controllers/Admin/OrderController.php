<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\MarketCard99OrderSyncService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['user', 'service'])
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('q'), function ($query, $term) {
                $query->whereHas('user', function ($userQuery) use ($term) {
                    $userQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user',
            'service.formFields',
            'variant',
            'events' => fn ($query) => $query->oldest()->with('actor'),
        ]);
        $fieldLabels = $order->service->formFields->pluck('label', 'name_key');

        return view('admin.orders.show', compact('order', 'fieldLabels'));
    }

    public function update(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): RedirectResponse
    {
        $orderStatusService->updateStatus(
            $order,
            $request->input('status'),
            $request->input('admin_note'),
            $request->user()
        );

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'تم تحديث حالة الطلب.');
    }

    public function syncMarketCard99Statuses(Request $request, MarketCard99OrderSyncService $orderSyncService): RedirectResponse
    {
        $result = $orderSyncService->sync($request->user());

        if (!($result['ok'] ?? false)) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', $result['message'] ?? 'فشلت مزامنة حالات الطلبات.')
                ->with('orders_result', $result);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'تمت مزامنة حالات طلبات MarketCard99 بنجاح.')
            ->with('orders_result', $result);
    }
}
