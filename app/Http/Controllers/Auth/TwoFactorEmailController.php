<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailTwoFactorService;
use App\Services\SecurityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorEmailController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has(EmailTwoFactorService::SESSION_USER_ID)) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-email');
    }

    public function store(Request $request, EmailTwoFactorService $twoFactorService, SecurityLogger $securityLogger): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::find($request->session()->get(EmailTwoFactorService::SESSION_USER_ID));
        if (! $user) {
            $request->session()->forget([EmailTwoFactorService::SESSION_USER_ID, EmailTwoFactorService::SESSION_REMEMBER]);
            return redirect()->route('login');
        }

        if (! $twoFactorService->verify($user, $request->string('code')->toString())) {
            $securityLogger->failedLogin($user->email, $user, 'invalid_two_factor_code', $request);

            throw ValidationException::withMessages([
                'code' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
            ]);
        }

        Auth::login($user, (bool) $request->session()->get(EmailTwoFactorService::SESSION_REMEMBER, false));
        $request->session()->forget([EmailTwoFactorService::SESSION_USER_ID, EmailTwoFactorService::SESSION_REMEMBER]);
        $request->session()->regenerate();
        $securityLogger->log('login_2fa', $user, $request);

        return redirect()->intended(route('home', absolute: false));
    }

    public function resend(Request $request, EmailTwoFactorService $twoFactorService): RedirectResponse
    {
        $user = User::find($request->session()->get(EmailTwoFactorService::SESSION_USER_ID));
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $twoFactorService->sendCode($user)) {
            return back()->withErrors([
                'code' => __('messages.mail_delivery_unavailable'),
            ]);
        }

        return back()->with('status', 'تم إرسال رمز جديد إلى بريدك الإلكتروني.');
    }
}
