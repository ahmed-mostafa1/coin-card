<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\Order;
use Illuminate\View\View;

class OpsController extends Controller
{
    public function index(): View
    {
        $search = request('q');
        $depositStatus = request('deposit_status', DepositRequest::STATUS_PENDING);
        $orderStatus = request('order_status', Order::STATUS_NEW);

        $deposits = DepositRequest::query()
            ->with(['user', 'paymentMethod'])
            ->when($depositStatus, fn ($query) => $query->where('status', $depositStatus))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(10)
            ->get();

        $orders = Order::query()
            ->with(['user', 'service'])
            ->when($orderStatus, fn ($query) => $query->where('status', $orderStatus))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(10)
            ->get();

        $depositCounts = DepositRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $orderCounts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.ops.index', compact(
            'deposits',
            'orders',
            'depositStatus',
            'orderStatus',
            'search',
            'depositCounts',
            'orderCounts'
        ));
    }
}
