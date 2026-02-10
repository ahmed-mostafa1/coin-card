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

        $transaction = $walletService->credit($wallet, (string) $data['amount'], [
            'type' => WalletTransaction::TYPE_DEPOSIT,
            'reference_type' => 'admin_manual_credit',
            'note' => $data['note'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'approved_by_user_id' => $request->user()?->id,
            'approved_at' => now(),
        ]);

        $wallet->refresh();
        $user->notify(new \App\Notifications\BalanceAdjustedNotification(
            $transaction,
            'credit',
            $data['note'] ?? null,
            $wallet->balance
        ));

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تمت إضافة الرصيد بنجاح.');
    }

    public function debit(User $user, Request $request, WalletService $walletService): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $wallet = $user->wallet()->firstOrCreate([]);

        // Check balance
        if ($wallet->balance < $data['amount']) {
             return redirect()->route('admin.users.show', $user)
                ->withErrors(['amount' => 'رصيد المستخدم غير كافٍ للخصم.']); // Insufficient balance
        }

        $transaction = $walletService->debit($wallet, (string) $data['amount'], [
            'type' => 'manual_withdraw', // Custom type for manual admin debit
            'reference_type' => 'admin_manual_debit',
            'note' => $data['note'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'approved_by_user_id' => $request->user()?->id,
            'approved_at' => now(),
        ]);

        $wallet->refresh();
        $user->notify(new \App\Notifications\BalanceAdjustedNotification(
            $transaction,
            'debit',
            $data['note'] ?? null,
            $wallet->balance
        ));

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم خصم الرصيد بنجاح.');
    }

    public function sendEmail(User $user, Request $request): RedirectResponse
    {
        if (!$user->email) {
            return redirect()->route('admin.users.show', $user)
                ->with('error', 'حساب هذا المستخدم ليس مربوط بأي بريد إلكتروني');
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AdminUserMessage($data['subject'], $data['message']));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.show', $user)
                ->with('error', 'فشل إرسال البريد الإلكتروني: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم إرسال البريد الإلكتروني بنجاح.');
    }

    public function sendNotification(User $user, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
        ]);

        $user->notify(new \App\Notifications\AdminGeneralNotification(
            $data['title_ar'],
            $data['title_en'],
            $data['content_ar'],
            $data['content_en']
        ));

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم إرسال الإشعار بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'تم حذف المستخدم بنجاح.');
    }
}
