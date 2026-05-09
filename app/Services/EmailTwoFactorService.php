<?php

namespace App\Services;

use App\Mail\GenericStyledMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailTwoFactorService
{
    public const SESSION_USER_ID = 'two_factor_email_user_id';
    public const SESSION_REMEMBER = 'two_factor_email_remember';

    public function sendCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'two_factor_email_code_hash' => Hash::make($code),
            'two_factor_email_code_expires_at' => now()->addMinutes(10),
            'two_factor_email_code_attempts' => 0,
        ])->save();

        try {
            Mail::to($user->email)->send(new GenericStyledMail(
                emailSubject: 'رمز التحقق الثنائي / Two-Factor Code',
                arTitle: 'رمز التحقق الثنائي',
                enTitle: 'Two-Factor Verification Code',
                arIntroLines: [
                    'رمز الدخول الخاص بك هو: '.$code,
                    'ينتهي هذا الرمز خلال 10 دقائق.',
                ],
                enIntroLines: [
                    'Your verification code is: '.$code,
                    'This code expires in 10 minutes.',
                ],
                arOutroLines: ['إذا لم تحاول تسجيل الدخول، يرجى تغيير كلمة المرور والتواصل مع الإدارة.'],
                enOutroLines: ['If you did not attempt to log in, please change your password and contact support.'],
            ));
        } catch (\Throwable $e) {
            Log::error('2FA email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function verify(User $user, string $code): bool
    {
        if (! $user->two_factor_email_code_hash || ! $user->two_factor_email_code_expires_at) {
            return false;
        }

        if ($user->two_factor_email_code_expires_at->isPast()) {
            return false;
        }

        if ((int) $user->two_factor_email_code_attempts >= 5) {
            return false;
        }

        $valid = Hash::check($code, $user->two_factor_email_code_hash);

        if (! $valid) {
            $user->forceFill([
                'two_factor_email_code_attempts' => (int) $user->two_factor_email_code_attempts + 1,
            ])->save();

            return false;
        }

        $this->clearCode($user);

        return true;
    }

    public function clearCode(User $user): void
    {
        $user->forceFill([
            'two_factor_email_code_hash' => null,
            'two_factor_email_code_expires_at' => null,
            'two_factor_email_code_attempts' => 0,
        ])->save();
    }
}
