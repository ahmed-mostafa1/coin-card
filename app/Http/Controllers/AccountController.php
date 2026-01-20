<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        $unreadNotificationsCount = auth()->user()->unreadNotifications()->count();

        return view('account', compact('wallet', 'unreadNotificationsCount'));
    }
}
