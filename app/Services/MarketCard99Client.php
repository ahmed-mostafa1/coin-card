<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MarketCard99Client
{
    private string $baseUrl;
    private string $token;
    private int $timeout = 25;
    private int $retryTimes = 2;
    private int $retryDelay = 500;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.marketcard99.base_url'), '/');
        $this->token = config('services.marketcard99.token');
    }

    /**
     * Fetch all products from MarketCard99
     */
    public function getProducts(): array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->get("{$this->baseUrl}/api/v2/products");

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::error('MarketCard99: Failed to fetch products', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('MarketCard99: Exception fetching products', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Create a bill on MarketCard99
     * 
     * @param int $productId
     * @param string|null $customerIdentifier
     * @param string|null $amount
     * @param string|null $purchasePassword
     * @return array ['success' => bool, 'message' => string]
     */
    public function createBill(
        int $productId,
        ?string $customerIdentifier = null,
        ?string $amount = null,
        ?string $purchasePassword = null
    ): array {
        try {
            // Build multipart payload
            $payload = [
                ['name' => 'product_id', 'contents' => (string) $productId],
            ];

            // Send both id_user and customer_id for compatibility
            if ($customerIdentifier) {
                $payload[] = ['name' => 'id_user', 'contents' => $customerIdentifier];
                $payload[] = ['name' => 'customer_id', 'contents' => $customerIdentifier];
            }

            if ($amount) {
                $payload[] = ['name' => 'amount', 'contents' => $amount];
            }

            if ($purchasePassword) {
                $payload[] = ['name' => 'old', 'contents' => $purchasePassword];
            }

            $response = Http::withToken($this->token)
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->asMultipart()
                ->post("{$this->baseUrl}/api/v2/bills", $payload);

            // MarketCard99 returns empty response on success
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Bill created successfully',
                ];
            }

            Log::error('MarketCard99: Failed to create bill', [
                'product_id' => $productId,
                'customer_identifier' => $customerIdentifier,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create bill: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('MarketCard99: Exception creating bill', [
                'product_id' => $productId,
                'customer_identifier' => $customerIdentifier,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all bills
     */
    public function getBills(): array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->get("{$this->baseUrl}/api/v2/bills");

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::error('MarketCard99: Failed to fetch bills', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('MarketCard99: Exception fetching bills', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get a specific bill by ID
     */
    public function getBill(int $billId): ?array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->get("{$this->baseUrl}/api/v2/bills/{$billId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('MarketCard99: Failed to fetch bill', [
                'bill_id' => $billId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('MarketCard99: Exception fetching bill', [
                'bill_id' => $billId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Resolve bill ID after creation by polling GET /bills
     * 
     * CRITICAL: Because POST /bills returns empty response, we must poll
     * GET /bills to find the newly created bill by matching criteria
     * 
     * @param int $productId
     * @param string|null $customerIdentifier
     * @param Carbon $createdAtLocal Local timestamp when we created the bill
     * @return array|null ['external_bill_id', 'external_uuid', 'external_status'] or null
     */
    public function resolveBillIdAfterCreate(
        int $productId,
        ?string $customerIdentifier,
        Carbon $createdAtLocal
    ): ?array {
        $maxAttempts = 6;
        $sleepMs = 1300; // 1.3 seconds between attempts

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            usleep($sleepMs * 1000); // Convert to microseconds

            $bills = $this->getBills();

            if (empty($bills)) {
                Log::warning('MarketCard99: No bills returned during resolution', [
                    'attempt' => $attempt,
                    'product_id' => $productId,
                ]);
                continue;
            }

            // Filter and find matching bill
            $matches = [];
            $threeMinutesAgo = $createdAtLocal->copy()->subMinutes(3);

            foreach ($bills as $bill) {
                // Match product ID
                if (!isset($bill['product']['id']) || $bill['product']['id'] != $productId) {
                    continue;
                }

                // Match customer_id if provided
                if ($customerIdentifier && isset($bill['customer_id'])) {
                    if ($bill['customer_id'] != $customerIdentifier) {
                        continue;
                    }
                }

                // Check created_at is within last 3 minutes
                if (isset($bill['created_at'])) {
                    try {
                        // Parse format: "Y-m-d h:i a" e.g., "2026-02-08 02:30 PM"
                        $billCreatedAt = Carbon::createFromFormat('Y-m-d h:i a', $bill['created_at']);
                        
                        if ($billCreatedAt->lt($threeMinutesAgo)) {
                            continue; // Too old
                        }
                    } catch (\Exception $e) {
                        // If parse fails, ignore time check and continue
                        Log::warning('MarketCard99: Failed to parse bill created_at', [
                            'created_at' => $bill['created_at'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $matches[] = $bill;
            }

            if (!empty($matches)) {
                // Sort by id desc and take first (newest)
                usort($matches, function ($a, $b) {
                    return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
                });

                $match = $matches[0];

                Log::info('MarketCard99: Bill resolved successfully', [
                    'attempt' => $attempt,
                    'bill_id' => $match['id'] ?? null,
                    'product_id' => $productId,
                    'customer_identifier' => $customerIdentifier,
                ]);

                return [
                    'external_bill_id' => $match['id'] ?? null,
                    'external_uuid' => $match['id_bill'] ?? null,
                    'external_status' => $match['status'] ?? null,
                    'external_raw' => $match,
                ];
            }

            Log::info('MarketCard99: No matching bill found, retrying...', [
                'attempt' => $attempt,
                'product_id' => $productId,
                'customer_identifier' => $customerIdentifier,
            ]);
        }

        Log::error('MarketCard99: Failed to resolve bill ID after max attempts', [
            'product_id' => $productId,
            'customer_identifier' => $customerIdentifier,
            'max_attempts' => $maxAttempts,
        ]);

        return null;
    }
}
