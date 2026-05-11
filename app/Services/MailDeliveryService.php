<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class MailDeliveryService
{
    public function send(string|object $recipient, Mailable $mail, string $purpose, ?int $userId = null): bool
    {
        $recipientKey = $this->recipientKey($recipient);

        if ($this->isLimited('mail:global', (int) config('mail.rate_limits.global_per_minute', 25))) {
            $this->logThrottled($purpose, $recipientKey, $userId, 'global');

            return false;
        }

        if ($recipientKey !== null && $this->isLimited('mail:recipient:'.$recipientKey, (int) config('mail.rate_limits.recipient_per_minute', 3))) {
            $this->logThrottled($purpose, $recipientKey, $userId, 'recipient');

            return false;
        }

        RateLimiter::hit('mail:global', 60);

        if ($recipientKey !== null) {
            RateLimiter::hit('mail:recipient:'.$recipientKey, 60);
        }

        try {
            Mail::to($recipient)->send($mail);

            return true;
        } catch (Throwable $exception) {
            $this->reportFailure($exception, $purpose, $recipientKey, $userId);

            return false;
        }
    }

    public function reportFailure(Throwable $exception, string $purpose, ?string $recipientKey = null, ?int $userId = null): void
    {
        $context = [
            'purpose' => $purpose,
            'recipient_key' => $recipientKey,
            'user_id' => $userId,
            'exception_class' => $exception::class,
            'message' => $exception->getMessage(),
        ];

        if ($this->isProviderRateLimit($exception)) {
            Log::warning('Mail delivery skipped because the SMTP provider rate limit was reached.', $context);

            return;
        }

        Log::warning('Mail delivery failed.', $context);
    }

    private function isLimited(string $key, int $maxAttempts): bool
    {
        return $maxAttempts > 0 && RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    private function recipientKey(string|object $recipient): ?string
    {
        $email = is_string($recipient) ? $recipient : ($recipient->email ?? null);

        return $email ? sha1(strtolower((string) $email)) : null;
    }

    private function logThrottled(string $purpose, ?string $recipientKey, ?int $userId, string $scope): void
    {
        Log::warning('Mail delivery skipped by application rate limiter.', [
            'purpose' => $purpose,
            'recipient_key' => $recipientKey,
            'user_id' => $userId,
            'scope' => $scope,
        ]);
    }

    private function isProviderRateLimit(Throwable $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'ratelimit')
            || str_contains($message, 'rate limit')
            || str_contains($message, '451 4.7.1');
    }
}
