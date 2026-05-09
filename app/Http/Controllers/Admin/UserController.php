<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\AdminSentNotification;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\SecurityLogger;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function show(User $user, LoyaltyService $loyaltyService): View
    {
        $user->load(['roles', 'wallet']);
        $wallet = $user->wallet()->firstOrCreate([]);

        $loyaltySummary = $loyaltyService->summary($user);

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

        return view('admin.users.show', compact('user', 'wallet', 'transactions', 'deposits', 'orders', 'loyaltySummary'));
    }

    public function security(User $user, Request $request): View
    {
        $logs = $user->securityLogs()
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->input('action')))
            ->when($request->filled('ip'), fn ($query) => $query->where('ip_address', 'like', '%'.$request->input('ip').'%'))
            ->when($request->filled('country'), fn ($query) => $query->where('country_code', $request->input('country')))
            ->when($request->filled('device'), fn ($query) => $query->where('device_type', $request->input('device')))
            ->when($request->filled('order_id'), fn ($query) => $query->where('order_id', $request->integer('order_id')))
            ->when($request->filled('deposit_request_id'), fn ($query) => $query->where('deposit_request_id', $request->integer('deposit_request_id')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $ipHistories = $user->ipHistories()->latest('last_seen_at')->limit(50)->get();
        $failedAttempts = $user->failedLoginAttempts()->latest()->limit(30)->get();
        $suspiciousLogs = $user->suspiciousActivityLogs()->latest()->limit(30)->get();
        $actions = $user->securityLogs()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.users.security', compact('user', 'logs', 'ipHistories', 'failedAttempts', 'suspiciousLogs', 'actions'));
    }

    public function changePassword(User $user, Request $request, SecurityLogger $securityLogger): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $securityLogger->log('admin_password_changed', $user, $request, [
            'admin_id' => $request->user()?->id,
            'target_user_id' => $user->id,
            'reason' => $data['reason'] ?? null,
        ], $user, $request->user());

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم تغيير كلمة مرور المستخدم وتسجيل العملية.');
    }

    public function toggleBan(User $user, Request $request): RedirectResponse
    {
        if ($user->is_banned) {
            $user->forceFill(['is_banned' => false, 'banned_at' => null])->save();
            app(SecurityLogger::class)->log('admin_user_unbanned', $user, $request, [], $user, $request->user());
        } else {
            $user->forceFill(['is_banned' => true, 'banned_at' => now()])->save();
            app(SecurityLogger::class)->log('admin_user_banned', $user, $request, [], $user, $request->user());
        }

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم تحديث حالة الحظر بنجاح.');
    }

    public function toggleFreeze(User $user, Request $request): RedirectResponse
    {
        if ($user->is_frozen) {
            $user->forceFill(['is_frozen' => false, 'frozen_at' => null])->save();
            app(SecurityLogger::class)->log('admin_user_unfrozen', $user, $request, [], $user, $request->user());
        } else {
            $user->forceFill(['is_frozen' => true, 'frozen_at' => now()])->save();
            app(SecurityLogger::class)->log('admin_user_frozen', $user, $request, [], $user, $request->user());
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
        app(SecurityLogger::class)->log('admin_balance_credit', $user, $request, [
            'wallet_transaction_id' => $transaction->id,
            'amount' => $data['amount'],
        ], $transaction, $request->user());

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
        app(SecurityLogger::class)->log('admin_balance_debit', $user, $request, [
            'wallet_transaction_id' => $transaction->id,
            'amount' => $data['amount'],
        ], $transaction, $request->user());

        $user->notify(new \App\Notifications\BalanceAdjustedNotification(
            $transaction,
            'debit',
            $data['note'] ?? null,
            $wallet->balance
        ));

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم خصم الرصيد بنجاح.');
    }

    public function refundHeld(User $user, Request $request, WalletService $walletService): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'note'   => ['required', 'string', 'max:500'],
        ]);

        $wallet = $user->wallet()->firstOrCreate([]);

        if ($wallet->held_balance < $data['amount']) {
            return redirect()->route('admin.users.show', $user)
                ->withErrors(['amount' => 'الرصيد المعلّق أقل من المبلغ المطلوب استرداده.']);
        }

        $transaction = $walletService->releaseHeldAmount($wallet, (string) $data['amount'], [
            'type'               => WalletTransaction::TYPE_RELEASE,
            'reference_type'     => 'admin_held_refund',
            'note'               => $data['note'],
            'created_by_user_id' => $request->user()?->id,
            'approved_by_user_id'=> $request->user()?->id,
            'approved_at'        => now(),
        ]);

        $wallet->refresh();
        app(SecurityLogger::class)->log('admin_held_refund', $user, $request, [
            'wallet_transaction_id' => $transaction->id,
            'amount' => $data['amount'],
            'note'   => $data['note'],
        ], $transaction, $request->user());

        $user->notify(new \App\Notifications\BalanceAdjustedNotification(
            $transaction,
            'credit',
            $data['note'],
            $wallet->balance
        ));

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم إرجاع الرصيد المعلّق إلى رصيد المستخدم بنجاح.');
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

    public function updateVerificationDiscount(User $user, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'verification_discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_verified' => ['nullable', 'boolean'],
        ]);

        $isVerified = $request->boolean('is_verified');
        $user->forceFill([
            'is_verified' => $isVerified,
            'verified_at' => $isVerified ? ($user->verified_at ?: now()) : null,
            'verification_discount_percentage' => $data['verification_discount_percentage'],
        ])->save();

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'تم تحديث بيانات التوثيق والخصم.');
    }

    public function sendNotification(User $user, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('notifications', 'public')
            : null;

        $user->notify(new \App\Notifications\AdminGeneralNotification(
            $data['title_ar'],
            $data['title_en'],
            $data['content_ar'],
            $data['content_en'],
            $imagePath
        ));

        AdminSentNotification::create([
            'admin_user_id' => $request->user()?->id,
            'target_user_id' => $user->id,
            'scope' => AdminSentNotification::SCOPE_SINGLE,
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'content_ar' => $data['content_ar'],
            'content_en' => $data['content_en'],
            'image_path' => $imagePath,
            'recipient_count' => 1,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        app(SecurityLogger::class)->log('admin_notification_sent', $user, $request, [
            'admin_id' => $request->user()?->id,
            'image_path' => $imagePath,
        ], $user, $request->user());

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
