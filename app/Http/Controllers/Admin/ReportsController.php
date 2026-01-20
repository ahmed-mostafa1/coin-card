<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\Order;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        $preset = request('preset');
        $fromInput = request('from');
        $toInput = request('to');

        if ($preset === 'today') {
            $fromDate = now()->startOfDay();
            $toDate = now()->endOfDay();
        } elseif ($preset === '30') {
            $fromDate = now()->subDays(29)->startOfDay();
            $toDate = now()->endOfDay();
        } elseif ($preset === '7') {
            $fromDate = now()->subDays(6)->startOfDay();
            $toDate = now()->endOfDay();
        } else {
            $fromDate = $fromInput ? Carbon::parse($fromInput)->startOfDay() : now()->subDays(6)->startOfDay();
            $toDate = $toInput ? Carbon::parse($toInput)->endOfDay() : now()->endOfDay();
        }

        $from = $fromDate->format('Y-m-d');
        $to = $toDate->format('Y-m-d');

        $depositBase = DepositRequest::query()->whereBetween('created_at', [$fromDate, $toDate]);
        $depositTotal = (clone $depositBase)->count();
        $depositCounts = (clone $depositBase)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $depositApprovedSum = (clone $depositBase)
            ->where('status', DepositRequest::STATUS_APPROVED)
            ->sum('approved_amount');

        $orderBase = Order::query()->whereBetween('created_at', [$fromDate, $toDate]);
        $orderTotal = (clone $orderBase)->count();
        $orderCounts = (clone $orderBase)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $settledRevenue = Order::query()
            ->where('status', Order::STATUS_DONE)
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('settled_at', [$fromDate, $toDate])
                    ->orWhere(function ($fallback) use ($fromDate, $toDate) {
                        $fallback->whereNull('settled_at')
                            ->whereBetween('updated_at', [$fromDate, $toDate]);
                    });
            })
            ->sum('amount_held');

        $totalHeld = Wallet::query()->sum('held_balance');
        $totalBalance = Wallet::query()->sum('balance');

        $topServices = Order::query()
            ->join('services', 'services.id', '=', 'orders.service_id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->selectRaw('orders.service_id, services.name, count(*) as total')
            ->groupBy('orders.service_id', 'services.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topUsers = Order::query()
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', Order::STATUS_DONE)
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('orders.settled_at', [$fromDate, $toDate])
                    ->orWhere(function ($fallback) use ($fromDate, $toDate) {
                        $fallback->whereNull('orders.settled_at')
                            ->whereBetween('orders.updated_at', [$fromDate, $toDate]);
                    });
            })
            ->selectRaw('orders.user_id, users.name, users.email, sum(orders.amount_held) as total')
            ->groupBy('orders.user_id', 'users.name', 'users.email')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'from',
            'to',
            'preset',
            'depositTotal',
            'depositCounts',
            'depositApprovedSum',
            'orderTotal',
            'orderCounts',
            'settledRevenue',
            'totalHeld',
            'totalBalance',
            'topServices',
            'topUsers'
        ));
    }
}
