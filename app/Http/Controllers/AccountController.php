<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $wallet = $user->wallet()->firstOrCreate([]);
        $unreadNotificationsCount = auth()->user()->unreadNotifications()->count();
        $recentOrders = $user->orders()
            ->with(['service', 'variant'])
            ->latest()
            ->limit(5)
            ->get();
        $recentDeposits = $user->depositRequests()
            ->with('paymentMethod')
            ->latest()
            ->limit(5)
            ->get();

        return view('account', compact('wallet', 'unreadNotificationsCount', 'recentOrders', 'recentDeposits'));
    }
}
