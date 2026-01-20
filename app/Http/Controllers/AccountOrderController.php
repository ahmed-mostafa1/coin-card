<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Authorize\Access\AuthorizationException;
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
        $fieldLabels = $order->service->formFields->pluck('label', 'name_key');

        return view('account.orders.show', compact('order', 'fieldLabels'));
    }
}
