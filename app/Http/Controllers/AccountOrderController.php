<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\View;

class AccountOrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['service', 'variant'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('account.orders.index', compact('orders'));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load([
            'service.formFields',
            'variant',
            'events' => fn ($query) => $query->oldest()->with('actor'),
        ]);
        $fieldLabels = $order->service->formFields
            ->pluck('label', 'name_key')
            ->merge([
                'offer_image_path' => __('messages.offer_image'),
                'offer_amount' => __('messages.offer_amount_label'),
                'service_discount_percent' => __('messages.service_discount_percent_label'),
                'payable_after_discount' => __('messages.payable_after_discount_label'),
                'quantity' => __('messages.quantity'),
            ]);

        return view('account.orders.show', compact('order', 'fieldLabels'));
    }
}
