<?php

namespace App\Services;

use App\Contracts\IpGeolocationServiceInterface;
use App\Models\FailedLoginAttempt;
use App\Models\SecurityLog;
use App\Models\SuspiciousActivityLog;
use App\Models\User;
use App\Models\UserIpHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityLogger
{
    public function __construct(private readonly IpGeolocationServiceInterface $geolocation) {}

    public function log(string $action, ?User $user = null, ?Request $request = null, array $meta = [], ?Model $subject = null, ?User $actor = null): ?SecurityLog
    {
        $request ??= request();

        try {
            $context = $this->context($request);
            $log = SecurityLog::create([
                'user_id' => $user?->id,
                'actor_user_id' => $actor?->id,
                'action' => $action,
                ...$context,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'order_id' => $meta['order_id'] ?? ($subject instanceof \App\Models\Order ? $subject->id : null),
                'deposit_request_id' => $meta['deposit_request_id'] ?? ($subject instanceof \App\Models\DepositRequest ? $subject->id : null),
                'wallet_transaction_id' => $meta['wallet_transaction_id'] ?? ($subject instanceof \App\Models\WalletTransaction ? $subject->id : null),
                'metadata' => array_diff_key($meta, array_flip(['order_id', 'deposit_request_id', 'wallet_transaction_id'])),
            ]);

            if ($user && filled($context['ip_address'] ?? null)) {
                $this->trackIp($user, $context);
                $this->detectSuspiciousIpActivity($user, $context);
                $this->detectTransactionBurst($user, $action);
            }

            return $log;
        } catch (\Throwable $e) {
            Log::warning('Security log failed', [
                'action' => $action,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function failedLogin(?string $email, ?User $user, string $reason, ?Request $request = null): void
    {
        $request ??= request();

        try {
            $context = $this->context($request);

            FailedLoginAttempt::create([
                'user_id' => $user?->id,
                'email' => $email,
                'ip_address' => $context['ip_address'],
                'country_code' => $context['country_code'],
                'country' => $context['country'],
                'device_type' => $context['device_type'],
                'operating_system' => $context['operating_system'],
                'browser' => $context['browser'],
                'user_agent' => $context['user_agent'],
                'reason' => $reason,
            ]);

            $this->log('failed_login', $user, $request, ['email' => $email, 'reason' => $reason]);
            $this->detectTooManyFailedLogins($email, $context);
        } catch (\Throwable $e) {
            Log::warning('Failed login log failed', ['email' => $email, 'error' => $e->getMessage()]);
        }
    }

    public function suspicious(?string $type, string $title, ?User $user = null, array $meta = [], string $severity = SuspiciousActivityLog::SEVERITY_MEDIUM): void
    {
        try {
            $request = request();
            $context = $this->context($request);

            SuspiciousActivityLog::create([
                'user_id' => $user?->id,
                'type' => (string) $type,
                'severity' => $severity,
                'title' => $title,
                'description' => $meta['description'] ?? null,
                'ip_address' => $meta['ip_address'] ?? $context['ip_address'],
                'country_code' => $meta['country_code'] ?? $context['country_code'],
                'metadata' => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Suspicious activity log failed', ['type' => $type, 'error' => $e->getMessage()]);
        }
    }

    /** @return array<string, mixed> */
    public function context(?Request $request = null): array
    {
        $request ??= request();
        $ip = $request->ip();
        $location = $this->safeLookup($ip);
        $agent = (string) $request->userAgent();
        $device = $this->parseUserAgent($agent);

        return [
            'ip_address' => $ip,
            'country_code' => $location['country_code'] ?? null,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'device_type' => $device['device_type'],
            'operating_system' => $device['operating_system'],
            'browser' => $device['browser'],
            'user_agent' => $agent ?: null,
            'language' => $request->header('Accept-Language'),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        ];
    }

    /** @param array<string, mixed> $context */
    private function trackIp(User $user, array $context): void
    {
        $ip = (string) $context['ip_address'];
        $now = now();

        $history = UserIpHistory::firstOrNew([
            'user_id' => $user->id,
            'ip_address' => $ip,
        ]);

        $history->fill([
            'country_code' => $context['country_code'] ?? null,
            'country' => $context['country'] ?? null,
            'city' => $context['city'] ?? null,
            'first_seen_at' => $history->exists ? ($history->first_seen_at ?: $now) : $now,
            'last_seen_at' => $now,
            'seen_count' => $history->exists ? ((int) $history->seen_count + 1) : 1,
        ])->save();
    }

    /** @param array<string, mixed> $context */
    private function detectSuspiciousIpActivity(User $user, array $context): void
    {
        $ip = $context['ip_address'] ?? null;
        if (! $ip) {
            return;
        }

        $sharedUsers = UserIpHistory::where('ip_address', $ip)->distinct('user_id')->count('user_id');
        if ($sharedUsers >= 3) {
            $this->suspicious('shared_ip', 'عدة حسابات تستخدم نفس عنوان IP', $user, [
                'ip_address' => $ip,
                'shared_user_count' => $sharedUsers,
            ], SuspiciousActivityLog::SEVERITY_MEDIUM);
        }

        $countries = UserIpHistory::where('user_id', $user->id)
            ->whereNotNull('country_code')
            ->where('last_seen_at', '>=', now()->subDays(7))
            ->distinct('country_code')
            ->count('country_code');

        if ($countries >= 3) {
            $this->suspicious('frequent_country_changes', 'تغيّر متكرر في بلد الدخول', $user, [
                'country_count_7_days' => $countries,
            ], SuspiciousActivityLog::SEVERITY_HIGH);
        }
    }

    /** @param array<string, mixed> $context */
    private function detectTooManyFailedLogins(?string $email, array $context): void
    {
        $ip = $context['ip_address'] ?? null;
        $recentByIp = $ip ? FailedLoginAttempt::where('ip_address', $ip)->where('created_at', '>=', now()->subMinutes(15))->count() : 0;
        $recentByEmail = $email ? FailedLoginAttempt::where('email', $email)->where('created_at', '>=', now()->subMinutes(15))->count() : 0;

        if ($recentByIp >= 5 || $recentByEmail >= 5) {
            $this->suspicious('too_many_failed_logins', 'محاولات دخول فاشلة كثيرة', null, [
                'email' => $email,
                'ip_address' => $ip,
                'recent_by_ip' => $recentByIp,
                'recent_by_email' => $recentByEmail,
            ], SuspiciousActivityLog::SEVERITY_HIGH);
        }
    }

    private function detectTransactionBurst(User $user, string $action): void
    {
        if (! in_array($action, ['order_created', 'deposit_created'], true)) {
            return;
        }

        $count = SecurityLog::where('user_id', $user->id)
            ->where('action', $action)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($count >= 6) {
            $this->suspicious('activity_burst', 'نمط نشاط مرتفع خلال وقت قصير', $user, [
                'action' => $action,
                'count_last_hour' => $count,
            ], SuspiciousActivityLog::SEVERITY_MEDIUM);
        }
    }

    /** @return array{country_code:?string,country:?string,city:?string} */
    private function safeLookup(?string $ip): array
    {
        try {
            return $this->geolocation->lookup($ip);
        } catch (\Throwable $e) {
            Log::debug('IP geolocation lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);

            return ['country_code' => null, 'country' => null, 'city' => null];
        }
    }

    /** @return array{device_type:string,operating_system:?string,browser:?string} */
    private function parseUserAgent(string $agent): array
    {
        $lower = Str::lower($agent);

        $deviceType = match (true) {
            str_contains($lower, 'bot') || str_contains($lower, 'crawler') || str_contains($lower, 'spider') => 'bot',
            str_contains($lower, 'tablet') || str_contains($lower, 'ipad') => 'tablet',
            str_contains($lower, 'mobile') || str_contains($lower, 'android') || str_contains($lower, 'iphone') => 'mobile',
            default => 'desktop',
        };

        $os = match (true) {
            str_contains($lower, 'windows') => 'Windows',
            str_contains($lower, 'mac os') || str_contains($lower, 'macintosh') => 'macOS',
            str_contains($lower, 'iphone') || str_contains($lower, 'ipad') => 'iOS',
            str_contains($lower, 'android') => 'Android',
            str_contains($lower, 'linux') => 'Linux',
            default => null,
        };

        $browser = match (true) {
            str_contains($lower, 'edg/') => 'Edge',
            str_contains($lower, 'opr/') || str_contains($lower, 'opera') => 'Opera',
            str_contains($lower, 'chrome/') => 'Chrome',
            str_contains($lower, 'safari/') && ! str_contains($lower, 'chrome/') => 'Safari',
            str_contains($lower, 'firefox/') => 'Firefox',
            default => null,
        };

        return compact('deviceType', 'os', 'browser') + [
            'device_type' => $deviceType,
            'operating_system' => $os,
        ];
    }
}
