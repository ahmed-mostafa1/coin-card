<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function show(User $user): View
    {
        $user->load(['roles', 'wallet']);
        $wallet = $user->wallet()->firstOrCreate([]);

        $transactions = $wallet->transactions()
            ->latest()
            ->limit(20)
            ->get();

        $deposits = $user->depositRequests()
            ->with('paymentMethod')
            ->latest()
            ->limit(20)
            ->get();

        $orders = $user->orders()
            ->with(['service', 'variant'])
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.users.show', compact('user', 'wallet', 'transactions', 'deposits', 'orders'));
    }
}
