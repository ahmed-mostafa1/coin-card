<?php

namespace App\Http\Controllers;

use App\Models\DepositRequest;
use Illuminate\View\View;

class AccountDepositController extends Controller
{
    public function index(): View
    {
        $deposits = DepositRequest::query()
            ->with('paymentMethod')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('account.deposits', compact('deposits'));
    }
}
