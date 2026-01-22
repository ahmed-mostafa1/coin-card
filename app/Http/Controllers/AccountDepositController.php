<?php

namespace App\Http\Controllers;

use App\Models\DepositRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class AccountDepositController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $deposits = DepositRequest::query()
            ->with('paymentMethod')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('account.deposits', compact('deposits'));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(DepositRequest $depositRequest): View
    {
        $this->authorize('view', $depositRequest);

        $depositRequest->load('paymentMethod');

        return view('account.deposits.show', compact('depositRequest'));
    }
}
