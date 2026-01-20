<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
        $order->load(['user', 'service.formFields']);
        $fieldLabels = $order->service->formFields->pluck('label', 'name_key');

        return view('admin.orders.show', compact('order', 'fieldLabels'));
    }

    public function update(Order $order): RedirectResponse
    {
        request()->validate([
            'status' => ['required', 'in:new,processing,done,rejected,cancelled'],
            'admin_note' => ['nullable', 'string'],
        ], [
            'status.required' => 'يرجى اختيار الحالة الجديدة.',
        ]);

        $order->update([
            'status' => request('status'),
            'admin_note' => request('admin_note'),
            'handled_by_user_id' => request()->user()->id,
            'handled_at' => now(),
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'تم تحديث حالة الطلب.');
    }
}
