<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarketCard99Client
{
    private const TOKEN_CACHE_KEY = 'marketcard99:auth:token';

    private string $baseUrl;
    private ?string $staticToken;
    private ?string $username;
    private ?string $password;
    private int $tokenCacheTtlMinutes;
    private int $timeout;
    private int $retryTimes;
    private int $retryDelay;
    private ?string $resolvedToken = null;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.marketcard99.base_url', 'https://app.market-card99.com'), '/');
        $this->staticToken = config('services.marketcard99.token');
        $this->username = config('services.marketcard99.username');
        $this->password = config('services.marketcard99.password');
        $this->tokenCacheTtlMinutes = (int) config('services.marketcard99.token_cache_ttl_minutes', 1440);
        $this->timeout = (int) config('services.marketcard99.timeout', 25);
        $this->retryTimes = (int) config('services.marketcard99.retry_times', 2);
        $this->retryDelay = (int) config('services.marketcard99.retry_delay_ms', 500);
    }

    public function getCategories(): array
    {
        return $this->requestJson('get', '/api/v2/categories');
    }

    public function getSubCategories(int $categoryId): array
    {
        return $this->requestJson('get', "/api/v2/categories/{$categoryId}");
    }

    public function getProductsByDepartment(int $departmentId): array
    {
        return $this->requestJson('get', "/api/v2/departments/{$departmentId}");
    }

    public function getProducts(): array
    {
        return $this->requestJson('get', '/api/v2/products');
    }

    /**
     * @param array{product_id:int|string,id_user?:string,customer_id?:string,amount?:string,old?:string} $payload
     * @return array{ok:bool,http_status:int|null,data:mixed,error_message:?string}
     */
    public function createBill(array $payload): array
    {
        if (!$this->hasConfiguredAuth()) {
            return $this->errorResult(null, 'MarketCard99 auth is missing. Set MARKETCARD99_USERNAME/PASSWORD or MARKETCARD99_TOKEN.');
        }

        $multipart = [];
        foreach ($payload as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $multipart[] = [
                'name' => (string) $key,
                'contents' => (string) $value,
            ];
        }

        try {
            $token = $this->getAccessToken();
            if (blank($token)) {
                return $this->errorResult(null, 'Unable to get MarketCard99 access token.');
            }

            $response = $this->buildRequest(true, $token)
                ->asMultipart()
                ->post($this->url('/api/v2/bills'), $multipart);

            if ($response->status() === 401 && $this->canUseCredentialLogin()) {
                $token = $this->getAccessToken(true);
                if (filled($token)) {
                    $response = $this->buildRequest(true, $token)
                        ->asMultipart()
                        ->post($this->url('/api/v2/bills'), $multipart);
                }
            }

            if ($response->successful()) {
                return [
                    'ok' => true,
                    'http_status' => $response->status(),
                    'data' => $response->json() ?? null,
                    'error_message' => null,
                ];
            }

            Log::error('MarketCard99: Failed to create bill', [
                'status' => $response->status(),
                'payload' => $payload,
                'body' => $response->body(),
            ]);

            return $this->errorResult($response->status(), $this->buildHttpErrorMessage($response->body()));
        } catch (\Throwable $e) {
            Log::error('MarketCard99: Exception creating bill', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResult(null, $e->getMessage());
        }
    }

    public function getBills(): array
    {
        return $this->requestJson('get', '/api/v2/bills', true);
    }

    public function getBill(int $billId): array
    {
        return $this->requestJson('get', "/api/v2/bills/{$billId}", true);
    }

    /**
     * Resolve bill ID after creation by polling GET /bills.
     *
     * @return array{external_bill_id:int|null,external_uuid:?string,external_status:?string,external_raw:mixed}|null
     */
    public function resolveBillIdAfterCreate(
        int $productId,
        ?string $customerIdentifier,
        Carbon $createdAtLocal
    ): ?array {
        $maxAttempts = 6;
        $sleepMs = 1300;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            usleep($sleepMs * 1000);

            $billsResponse = $this->getBills();
            if (!($billsResponse['ok'] ?? false)) {
                Log::warning('MarketCard99: Failed to fetch bills while resolving ID', [
                    'attempt' => $attempt,
                    'product_id' => $productId,
                    'error' => $billsResponse['error_message'] ?? null,
                ]);

                continue;
            }

            $bills = $this->extractBills($billsResponse['data'] ?? null);
            if (empty($bills)) {
                continue;
            }

            $matches = [];
            $threeMinutesAgo = $createdAtLocal->copy()->subMinutes(3);

            foreach ($bills as $bill) {
                if (!isset($bill['product']['id']) || (int) $bill['product']['id'] !== $productId) {
                    continue;
                }

                if ($customerIdentifier && isset($bill['customer_id']) && (string) $bill['customer_id'] !== $customerIdentifier) {
                    continue;
                }

                if (isset($bill['created_at']) && is_string($bill['created_at'])) {
                    try {
                        $billCreatedAt = Carbon::createFromFormat('Y-m-d h:i a', $bill['created_at']);
                        if ($billCreatedAt->lt($threeMinutesAgo)) {
                            continue;
                        }
                    } catch (\Throwable $e) {
                        Log::warning('MarketCard99: Failed parsing bill created_at', [
                            'created_at' => $bill['created_at'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $matches[] = $bill;
            }

            if (empty($matches)) {
                continue;
            }

            usort($matches, fn (array $a, array $b) => (($b['id'] ?? 0) <=> ($a['id'] ?? 0)));
            $match = $matches[0];

            return [
                'external_bill_id' => isset($match['id']) ? (int) $match['id'] : null,
                'external_uuid' => isset($match['id_bill']) ? (string) $match['id_bill'] : null,
                'external_status' => isset($match['status']) ? (string) $match['status'] : null,
                'external_raw' => $match,
            ];
        }

        return null;
    }

    private function requestJson(string $method, string $path, bool $requiresAuth = false): array
    {
        if ($requiresAuth && !$this->hasConfiguredAuth()) {
            return $this->errorResult(null, 'MarketCard99 auth is missing. Set MARKETCARD99_USERNAME/PASSWORD or MARKETCARD99_TOKEN.');
        }

        try {
            $response = null;
            if ($requiresAuth) {
                $token = $this->getAccessToken();
                if (blank($token)) {
                    return $this->errorResult(null, 'Unable to get MarketCard99 access token.');
                }

                $response = $this->buildRequest(true, $token)->send(strtoupper($method), $this->url($path));
                if ($response->status() === 401 && $this->canUseCredentialLogin()) {
                    $token = $this->getAccessToken(true);
                    if (filled($token)) {
                        $response = $this->buildRequest(true, $token)->send(strtoupper($method), $this->url($path));
                    }
                }
            } else {
                $response = $this->buildRequest(false)->send(strtoupper($method), $this->url($path));
            }

            if ($response->successful()) {
                return [
                    'ok' => true,
                    'http_status' => $response->status(),
                    'data' => $response->json(),
                    'error_message' => null,
                ];
            }

            Log::error('MarketCard99: API request failed', [
                'method' => strtoupper($method),
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->errorResult($response->status(), $this->buildHttpErrorMessage($response->body()));
        } catch (\Throwable $e) {
            Log::error('MarketCard99: API request exception', [
                'method' => strtoupper($method),
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResult(null, $e->getMessage());
        }
    }

    private function buildRequest(bool $withToken, ?string $token = null): PendingRequest
    {
        $request = Http::timeout($this->timeout)->retry($this->retryTimes, $this->retryDelay);

        if ($withToken) {
            $token ??= $this->getAccessToken();
            if (filled($token)) {
                $request = $request->withToken((string) $token);
            }
        }

        return $request;
    }

    private function url(string $path): string
    {
        return $this->baseUrl.'/'.ltrim($path, '/');
    }

    private function errorResult(?int $httpStatus, string $message): array
    {
        return [
            'ok' => false,
            'http_status' => $httpStatus,
            'data' => null,
            'error_message' => $message,
        ];
    }

    private function buildHttpErrorMessage(string $body): string
    {
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            return (string) ($decoded['msg'] ?? $decoded['message'] ?? $body);
        }

        return $body !== '' ? $body : 'HTTP request failed';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractBills(mixed $data): array
    {
        if (is_array($data) && array_is_list($data)) {
            return $data;
        }

        if (is_array($data)) {
            $bills = $data['data']['bills'] ?? $data['bills'] ?? null;
            if (is_array($bills)) {
                return $bills;
            }
        }

        return [];
    }

    private function hasConfiguredAuth(): bool
    {
        return $this->canUseCredentialLogin() || filled($this->staticToken);
    }

    private function canUseCredentialLogin(): bool
    {
        return filled($this->username) && filled($this->password);
    }

    private function getAccessToken(bool $forceRefresh = false): ?string
    {
        if (!$forceRefresh && filled($this->resolvedToken)) {
            return $this->resolvedToken;
        }

        if ($forceRefresh) {
            $this->resolvedToken = null;
            Cache::forget(self::TOKEN_CACHE_KEY);
        }

        if (!$forceRefresh) {
            $cached = Cache::get(self::TOKEN_CACHE_KEY);
            if (is_string($cached) && filled($cached)) {
                $this->resolvedToken = $cached;
                return $this->resolvedToken;
            }
        }

        if ($this->canUseCredentialLogin()) {
            $loginToken = $this->loginAndGetToken();
            if (filled($loginToken)) {
                $ttl = max($this->tokenCacheTtlMinutes, 1);
                Cache::put(self::TOKEN_CACHE_KEY, $loginToken, now()->addMinutes($ttl));
                $this->resolvedToken = $loginToken;
                return $this->resolvedToken;
            }
        }

        if (filled($this->staticToken)) {
            $this->resolvedToken = (string) $this->staticToken;
            return $this->resolvedToken;
        }

        return null;
    }

    private function loginAndGetToken(): ?string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->asMultipart()
                ->post($this->url('/api/v2/login'), [
                    ['name' => 'username', 'contents' => (string) $this->username],
                    ['name' => 'password', 'contents' => (string) $this->password],
                ]);

            if (!$response->successful()) {
                Log::error('MarketCard99: Login failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $payload = $response->json();
            $token = $this->extractTokenFromLoginPayload($payload);

            if (blank($token)) {
                Log::error('MarketCard99: Login response does not contain token', [
                    'payload' => $payload,
                ]);
                return null;
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('MarketCard99: Login exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function extractTokenFromLoginPayload(mixed $payload): ?string
    {
        if (!is_array($payload)) {
            return null;
        }

        $token = data_get($payload, 'data.token')
            ?? data_get($payload, 'token');

        return is_string($token) && filled($token) ? $token : null;
    }
}
