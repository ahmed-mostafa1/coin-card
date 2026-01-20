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
        $tab = request('tab', 'deposits');

        $depositSearch = request('deposit_q');
        $depositStatus = request('deposit_status', DepositRequest::STATUS_PENDING);
        $depositFrom = request('deposit_from');
        $depositTo = request('deposit_to');

        $orderSearch = request('order_q');
        $orderFrom = request('order_from');
        $orderTo = request('order_to');

        $pendingDepositsCount = DepositRequest::query()
            ->where('status', DepositRequest::STATUS_PENDING)
            ->count();
        $newOrdersCount = Order::query()
            ->where('status', Order::STATUS_NEW)
            ->count();
        $processingOrdersCount = Order::query()
            ->where('status', Order::STATUS_PROCESSING)
            ->count();

        $deposits = collect();
        $orders = collect();
        $orderStatus = null;

        if ($tab === 'deposits') {
            $deposits = DepositRequest::query()
                ->with(['user', 'paymentMethod'])
                ->when($depositStatus, fn ($query) => $query->where('status', $depositStatus))
                ->when($depositFrom, fn ($query) => $query->whereDate('created_at', '>=', $depositFrom))
                ->when($depositTo, fn ($query) => $query->whereDate('created_at', '<=', $depositTo))
                ->when($depositSearch, function ($query) use ($depositSearch) {
                    $query->where(function ($inner) use ($depositSearch) {
                        if (is_numeric($depositSearch)) {
                            $inner->orWhere('id', (int) $depositSearch);
                        }

                        $inner->orWhereHas('user', function ($userQuery) use ($depositSearch) {
                            $userQuery->where('name', 'like', "%{$depositSearch}%")
                                ->orWhere('email', 'like', "%{$depositSearch}%");
                        });
                    });
                })
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } elseif ($tab === 'orders_processing') {
            $orderStatus = Order::STATUS_PROCESSING;
        } else {
            $orderStatus = Order::STATUS_NEW;
        }

        if (in_array($tab, ['orders_new', 'orders_processing'], true)) {
            $orders = Order::query()
                ->with(['user', 'service', 'variant'])
                ->where('status', $orderStatus)
                ->when($orderFrom, fn ($query) => $query->whereDate('created_at', '>=', $orderFrom))
                ->when($orderTo, fn ($query) => $query->whereDate('created_at', '<=', $orderTo))
                ->when($orderSearch, function ($query) use ($orderSearch) {
                    $query->where(function ($inner) use ($orderSearch) {
                        if (is_numeric($orderSearch)) {
                            $inner->orWhere('id', (int) $orderSearch);
                        }

                        $inner->orWhereHas('user', function ($userQuery) use ($orderSearch) {
                            $userQuery->where('name', 'like', "%{$orderSearch}%")
                                ->orWhere('email', 'like', "%{$orderSearch}%");
                        });
                    });
                })
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.ops.index', compact(
            'tab',
            'deposits',
            'orders',
            'depositStatus',
            'depositSearch',
            'depositFrom',
            'depositTo',
            'orderSearch',
            'orderFrom',
            'orderTo',
            'orderStatus',
            'pendingDepositsCount',
            'newOrdersCount',
            'processingOrdersCount'
        ));
    }
}
