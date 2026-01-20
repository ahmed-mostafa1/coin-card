<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\View\View;

class AccountWalletController extends Controller
{
    public function index(): View
    {
        $transactions = WalletTransaction::query()
            ->whereHas('wallet', fn ($query) => $query->where('user_id', auth()->id()))
            ->latest()
            ->paginate(10);

        return view('account.wallet', compact('transactions'));
    }
}
