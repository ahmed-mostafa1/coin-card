<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailyCardClient
{
    private string $baseUrl;
    private ?string $apiKey;
    private ?string $secret;
    private int $timeout;
    private bool $enabled;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.dailycard.base_url', 'https://dailycard.shop/UAPI'), '/');
        $this->apiKey  = config('services.dailycard.api_key');
        $this->secret  = config('services.dailycard.secret');
        $this->timeout = (int) config('services.dailycard.timeout', 25);
        $this->enabled = (bool) config('services.dailycard.enabled', true);
    }

    public function isEnabled(): bool
    {
        return $this->enabled && filled($this->apiKey) && filled($this->secret);
    }

    /**
     * GET /api-keys/user/info/
     */
    public function getUserInfo(): array
    {
        return $this->get('/api-keys/user/info/');
    }

    /**
     * GET /api-keys/products/
     *
     * @param array{search?:string,category?:int,product_type?:string,page?:int,page_size?:int} $filters
     */
    public function getProducts(array $filters = []): array
    {
        return $this->get('/api-keys/products/', $filters);
    }

    /**
     * POST /api-keys/orders/create/
     *
     * @param array{product:int,account_id:string,quantity?:int,client_order_id?:string} $payload
     */
    public function createOrder(array $payload): array
    {
        return $this->post('/api-keys/orders/create/', $payload);
    }

    /**
     * GET /api-keys/orders/status/
     */
    public function getOrderStatus(string $clientOrderId): array
    {
        return $this->get('/api-keys/orders/status/', ['client_order_id' => $clientOrderId]);
    }

    /**
     * GET /api-keys/orders/history/
     */
    public function getOrderHistory(array $filters = []): array
    {
        return $this->get('/api-keys/orders/history/', $filters);
    }

    // ─── Private helpers ────────────────────────────────────────────────────────

    private function get(string $path, array $query = []): array
    {
        if (! $this->isEnabled()) {
            return $this->error(null, 'DailyCard integration is disabled or credentials are missing.');
        }

        try {
            $response = $this->buildRequest()->get($this->url($path), $query ?: null);

            return $this->parseResponse($response, $path);
        } catch (\Throwable $e) {
            Log::error('DailyCard: GET request exception', ['path' => $path, 'error' => $e->getMessage()]);
            return $this->error(null, $e->getMessage());
        }
    }

    private function post(string $path, array $data): array
    {
        if (! $this->isEnabled()) {
            return $this->error(null, 'DailyCard integration is disabled or credentials are missing.');
        }

        try {
            $response = $this->buildRequest()->post($this->url($path), $data);
            return $this->parseResponse($response, $path);
        } catch (\Throwable $e) {
            Log::error('DailyCard: POST request exception', ['path' => $path, 'error' => $e->getMessage()]);
            return $this->error(null, $e->getMessage());
        }
    }

    private function buildRequest(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout($this->timeout)
            ->withHeaders([
                'X-API-Key'    => (string) $this->apiKey,
                'X-API-Secret' => (string) $this->secret,
                'Content-Type' => 'application/json',
            ]);
    }


    private function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }

    private function parseResponse(\Illuminate\Http\Client\Response $response, string $path): array
    {
        if ($response->successful()) {
            return [
                'ok'            => true,
                'http_status'   => $response->status(),
                'data'          => $response->json(),
                'error_message' => null,
            ];
        }

        $body    = $response->body();
        $decoded = json_decode($body, true);
        $message = is_array($decoded)
            ? (string) ($decoded['detail'] ?? $decoded['message'] ?? $decoded['error'] ?? $body)
            : ($body ?: 'HTTP error ' . $response->status());

        Log::error('DailyCard: API error', [
            'path'    => $path,
            'status'  => $response->status(),
            'message' => $message,
        ]);

        return $this->error($response->status(), $message);
    }

    private function error(?int $httpStatus, string $message): array
    {
        return [
            'ok'            => false,
            'http_status'   => $httpStatus,
            'data'          => null,
            'error_message' => $message,
        ];
    }
}
