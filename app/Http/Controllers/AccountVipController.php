<?php

namespace App\Http\Controllers;

use App\Services\LoyaltyService;
use Illuminate\View\View;

class AccountVipController extends Controller
{
    public function show(LoyaltyService $loyaltyService): View
    {
        $user = auth()->user();
        $summary = $loyaltyService->summary($user);

        return view('account.vip', compact('summary'));
    }
}
