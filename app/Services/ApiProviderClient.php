<?php

namespace App\Services;

use App\Models\ApiProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Generic HTTP client that applies any configured auth strategy
 * and resolves dot-notation paths from JSON responses.
 */
class ApiProviderClient
{
    public function __construct(
        private readonly ApiProvider $provider
    ) {}

    public function get(string $path, array $query = []): array
    {
        try {
            $request = $this->buildRequest();
            $url     = $this->provider->fullUrl($path);

            if ($this->isQueryAuth()) {
                $query = array_merge($query, $this->queryAuthParams());
            }

            $response = $request->get($url, $query ?: null);
            return $this->parse($response, $path);
        } catch (\Throwable $e) {
            Log::error('ApiProvider GET failed', [
                'provider' => $this->provider->slug,
                'path'     => $path,
                'error'    => $e->getMessage(),
            ]);
            return $this->error(null, $e->getMessage());
        }
    }

    public function post(string $path, array $data): array
    {
        try {
            $request = $this->buildRequest();
            $url     = $this->provider->fullUrl($path);

            if ($this->isQueryAuth()) {
                $url .= (str_contains($url, '?') ? '&' : '?')
                    . http_build_query($this->queryAuthParams());
            }

            $response = $request->post($url, $data);
            return $this->parse($response, $path);
        } catch (\Throwable $e) {
            Log::error('ApiProvider POST failed', [
                'provider' => $this->provider->slug,
                'path'     => $path,
                'error'    => $e->getMessage(),
            ]);
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Resolve a dot-notation path from an array.
     * e.g. resolvePath($data, 'data.results') => $data['data']['results']
     * Empty path returns the array as-is.
     */
    public function resolvePath(mixed $data, ?string $dotPath): mixed
    {
        if (blank($dotPath) || ! is_array($data)) {
            return $data;
        }

        foreach (explode('.', $dotPath) as $key) {
            if (! is_array($data) || ! array_key_exists($key, $data)) {
                return null;
            }
            $data = $data[$key];
        }

        return $data;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildRequest(): PendingRequest
    {
        /** @var PendingRequest $request */
        $request = Http::timeout($this->provider->timeout)
            ->acceptJson()
            ->withHeaders(['Content-Type' => 'application/json']);

        return match ($this->provider->auth_type) {
            ApiProvider::AUTH_API_KEY_HEADER => $request->withHeaders([
                'X-API-Key'    => (string) $this->provider->getCredential('key'),
                'X-API-Secret' => (string) $this->provider->getCredential('secret'),
            ]),
            ApiProvider::AUTH_BASIC_HEADER => $request->withBasicAuth(
                (string) $this->provider->getCredential('username'),
                (string) $this->provider->getCredential('password'),
            ),
            ApiProvider::AUTH_BEARER_HEADER => $request->withToken(
                (string) $this->provider->getCredential('token'),
            ),
            // Query-based auth: credentials appended in get()/post()
            default => $request,
        };
    }

    private function isQueryAuth(): bool
    {
        return in_array($this->provider->auth_type, [
            ApiProvider::AUTH_API_KEY_QUERY,
            ApiProvider::AUTH_BASIC_QUERY,
        ], true);
    }

    private function queryAuthParams(): array
    {
        return match ($this->provider->auth_type) {
            ApiProvider::AUTH_API_KEY_QUERY => [
                'api_key' => (string) $this->provider->getCredential('key'),
                'secret'  => (string) $this->provider->getCredential('secret'),
            ],
            ApiProvider::AUTH_BASIC_QUERY => [
                'username' => (string) $this->provider->getCredential('username'),
                'password' => (string) $this->provider->getCredential('password'),
            ],
            default => [],
        };
    }

    private function parse(Response $response, string $path): array
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
            : ($body ?: 'HTTP ' . $response->status());

        Log::error('ApiProvider API error', [
            'provider' => $this->provider->slug,
            'path'     => $path,
            'status'   => $response->status(),
            'message'  => $message,
        ]);

        return $this->error($response->status(), $message);
    }

    private function error(?int $status, string $message): array
    {
        return [
            'ok'            => false,
            'http_status'   => $status,
            'data'          => null,
            'error_message' => $message,
        ];
    }
}
