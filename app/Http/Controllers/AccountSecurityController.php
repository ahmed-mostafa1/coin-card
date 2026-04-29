<?php

namespace App\Http\Controllers;

use App\Services\SecurityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountSecurityController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $recentLogs = $user->securityLogs()->latest()->limit(10)->get();

        return view('account.security', compact('recentLogs'));
    }

    public function enable(Request $request, SecurityLogger $securityLogger): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->forceFill([
            'two_factor_email_enabled' => true,
            'two_factor_email_enabled_at' => now(),
        ])->save();

        $securityLogger->log('two_factor_enabled', $user, $request);

        return back()->with('status', 'تم تفعيل التحقق الثنائي عبر البريد.');
    }

    public function disable(Request $request, SecurityLogger $securityLogger): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required_without:code', 'nullable', 'string'],
            'code' => ['nullable', 'digits:6'],
        ]);

        $user = $request->user();
        if (! empty($data['current_password']) && ! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->forceFill([
            'two_factor_email_enabled' => false,
            'two_factor_email_enabled_at' => null,
            'two_factor_email_code_hash' => null,
            'two_factor_email_code_expires_at' => null,
            'two_factor_email_code_attempts' => 0,
        ])->save();

        $securityLogger->log('two_factor_disabled', $user, $request);

        return back()->with('status', 'تم تعطيل التحقق الثنائي.');
    }
}
