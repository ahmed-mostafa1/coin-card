<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\EmailTwoFactorService;
use App\Services\SecurityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, EmailTwoFactorService $twoFactorService, SecurityLogger $securityLogger): RedirectResponse
    {
        $user = $request->authenticate();

        if ($user->two_factor_email_enabled) {
            $request->session()->put(EmailTwoFactorService::SESSION_USER_ID, $user->id);
            $request->session()->put(EmailTwoFactorService::SESSION_REMEMBER, $request->boolean('remember'));
            $twoFactorService->sendCode($user);

            return redirect()->route('two-factor.email.challenge')
                ->with('status', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();
        $securityLogger->log('login', $user, $request);

        return redirect()->intended(route('home', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            app(SecurityLogger::class)->log('logout', $user, $request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
