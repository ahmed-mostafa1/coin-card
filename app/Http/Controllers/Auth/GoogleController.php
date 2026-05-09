
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback(\App\Services\EmailTwoFactorService $twoFactorService): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Look up by google_id first (most reliable), then fall back to email
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            try {
                $user = User::create([
                    'name'      => $googleUser->getName() ?? $googleUser->getNickname() ?? 'مستخدم جديد',
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                    'password'  => Hash::make(Str::random(32)),
                ]);

                Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
                $user->assignRole('customer');
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle race condition: another request already created the user
                // (integrity constraint violation on email or google_id)
                if ($e->errorInfo[1] === 1062) {
                    $user = User::where('email', $googleUser->getEmail())->first();

                    if (! $user) {
                        // Extremely rare: google_id collision — just redirect to login
                        return redirect()->route('login')
                            ->with('status', 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.');
                    }
                } else {
                    throw $e;
                }
            }
        }

        // Always keep google_id and avatar in sync for existing users
        if ($user && (! $user->google_id || $user->google_id !== $googleUser->getId())) {
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ])->save();
        }

        if (! $user->hasAnyRole(['admin', 'customer'])) {
            Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
            $user->assignRole('customer');
        }


        if ($user->is_banned) {
            return redirect()->route('login')
                ->with('status', 'تم حظر حسابك. يرجى التواصل مع الإدارة.');
        }

        if ($user->two_factor_email_enabled) {
            session()->put(\App\Services\EmailTwoFactorService::SESSION_USER_ID, $user->id);
            session()->put(\App\Services\EmailTwoFactorService::SESSION_REMEMBER, true);
            $twoFactorService->sendCode($user);

            return redirect()->route('two-factor.email.challenge')
                ->with('status', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
        }

        Auth::login($user, true);
        app(\App\Services\SecurityLogger::class)->log('login_google', $user, request());

        return redirect()->route('home');
    }
}
