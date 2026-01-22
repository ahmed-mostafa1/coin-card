<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Services\VipService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();

        $users = User::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user, VipService $vipService): View
    {
        $user->load(['roles', 'wallet']);
        $wallet = $user->wallet()->firstOrCreate([]);

        $vipSummary = $vipService->getVipSummary($user);

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

        return view('admin.users.show', compact('user', 'wallet', 'transactions', 'deposits', 'orders', 'vipSummary'));
    }

    public function toggleBan(User $user, Request $request): RedirectResponse
    {
        if ($user->is_banned) {
            $user->forceFill(['is_banned' => false, 'banned_at' => null])->save();
        } else {
            $user->forceFill(['is_banned' => true, 'banned_at' => now()])->save();
        }

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم تحديث حالة الحظر بنجاح.');
    }

    public function toggleFreeze(User $user): RedirectResponse
    {
        if ($user->is_frozen) {
            $user->forceFill(['is_frozen' => false, 'frozen_at' => null])->save();
        } else {
            $user->forceFill(['is_frozen' => true, 'frozen_at' => now()])->save();
        }

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم تحديث حالة التجميد بنجاح.');
    }

    public function credit(User $user, Request $request, WalletService $walletService): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $wallet = $user->wallet()->firstOrCreate([]);

        $walletService->credit($wallet, (string) $data['amount'], [
            'type' => WalletTransaction::TYPE_DEPOSIT,
            'reference_type' => 'admin_manual_credit',
            'note' => $data['note'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'approved_by_user_id' => $request->user()?->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تمت إضافة الرصيد بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'تم حذف المستخدم بنجاح.');
    }
}
