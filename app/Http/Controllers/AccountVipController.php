<?php

namespace App\Http\Controllers;

use App\Models\VipTier;
use App\Services\VipService;
use Illuminate\View\View;

class AccountVipController extends Controller
{
    public function show(VipService $vipService): View
    {
        $user = auth()->user();
        $summary = $vipService->getVipSummary($user);

        $tiers = VipTier::query()
            ->where('is_active', true)
            ->orderBy('rank')
            ->get();

        return view('account.vip', compact('summary', 'tiers'));
    }
}
