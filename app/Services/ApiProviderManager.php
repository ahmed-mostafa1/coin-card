<?php

namespace App\Services;

use App\Models\ApiProvider;
use App\Models\Order;
use App\Models\Service;

/**
 * Resolves the correct provider services for a given order or service.
 */
class ApiProviderManager
{
    /**
     * Get an ApiProviderOrderService for the given order.
     * Returns null if the order's service has no linked provider.
     */
    public function forOrder(Order $order): ?ApiProviderOrderService
    {
        $provider = $order->service?->provider;

        if (! $provider instanceof ApiProvider || ! $provider->is_active) {
            return null;
        }

        return $this->makeOrderService($provider);
    }

    /**
     * Get an ApiProviderCatalogService for the given provider.
     */
    public function forProvider(ApiProvider $provider): ApiProviderCatalogService
    {
        $client = new ApiProviderClient($provider);
        return new ApiProviderCatalogService($provider, $client);
    }

    /**
     * Get an ApiProviderOrderService for a specific provider.
     */
    public function makeOrderService(ApiProvider $provider): ApiProviderOrderService
    {
        $client = new ApiProviderClient($provider);
        return new ApiProviderOrderService($provider, $client);
    }

    /**
     * Get an ApiProviderClient for a specific provider (for raw test calls).
     */
    public function clientFor(ApiProvider $provider): ApiProviderClient
    {
        return new ApiProviderClient($provider);
    }
}
