<?php

namespace App\Services;

use App\Models\ApiProvider;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProviderServiceStatusSyncService
{
    public function __construct(private readonly ApiProviderManager $manager) {}

    /** @return array{checked:int,changed:int,failed:int} */
    public function sync(?ApiProvider $onlyProvider = null): array
    {
        $totals = ['checked' => 0, 'changed' => 0, 'failed' => 0];

        $providers = ApiProvider::query()
            ->where('is_active', true)
            ->when($onlyProvider, fn ($query) => $query->whereKey($onlyProvider->id))
            ->whereHas('services', fn ($query) => $query->whereNotNull('external_product_id'))
            ->get();

        foreach ($providers as $provider) {
            $result = $this->syncProvider($provider);
            foreach ($totals as $key => $value) {
                $totals[$key] += $result[$key];
            }
        }

        return $totals;
    }

    /** @return array{checked:int,changed:int,failed:int} */
    public function syncProvider(ApiProvider $provider): array
    {
        $result = ['checked' => 0, 'changed' => 0, 'failed' => 0];

        try {
            $catalog = $this->fetchCatalog($provider);
        } catch (\Throwable $e) {
            Log::error('Provider status sync failed to fetch catalog', [
                'provider_id' => $provider->id,
                'provider_slug' => $provider->slug,
                'error' => $e->getMessage(),
            ]);

            Service::where('provider_id', $provider->id)->whereNotNull('external_product_id')->update([
                'provider_status_sync_error' => $e->getMessage(),
                'provider_status_synced_at' => now(),
            ]);

            $result['failed']++;
            return $result;
        }

        $productsByExternalId = collect($catalog['products'])->keyBy(fn (array $product) => (string) ($product['external_id'] ?? ''));

        Service::where('provider_id', $provider->id)
            ->whereNotNull('external_product_id')
            ->chunkById(200, function ($services) use ($provider, $productsByExternalId, &$result): void {
                foreach ($services as $service) {
                    $result['checked']++;
                    $product = $productsByExternalId->get((string) $service->external_product_id);

                    if (! $product) {
                        $changed = $this->applyStatus($service, 'removed', false, null, 'لم تعد الخدمة موجودة في كتالوج المزود.');
                        $result['changed'] += $changed ? 1 : 0;
                        continue;
                    }

                    $rawStatus = $product['availability_raw'] ?? $product['available'] ?? null;
                    $status = $this->mapStatus($rawStatus);
                    $available = in_array($status, ['available'], true);
                    $changed = $this->applyStatus($service, $status, $available, $rawStatus, null, $product['_raw'] ?? null);
                    $result['changed'] += $changed ? 1 : 0;
                }
            });

        Log::info('Provider service status sync completed', [
            'provider_id' => $provider->id,
            'provider_slug' => $provider->slug,
            ...$result,
        ]);

        return $result;
    }

    private function applyStatus(Service $service, string $status, bool $available, mixed $rawStatus = null, ?string $message = null, ?array $payload = null): bool
    {
        $oldStatus = $service->provider_status;
        $oldAvailable = $service->provider_is_available;

        $service->forceFill([
            'provider_status' => $status,
            'provider_status_raw' => is_scalar($rawStatus) || $rawStatus === null ? (string) $rawStatus : json_encode($rawStatus, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'provider_status_synced_at' => now(),
            'provider_status_message' => $message,
            'provider_status_sync_error' => null,
            'provider_availability_managed_by_provider' => true,
            'provider_is_available' => $available,
            'provider_last_synced_at' => now(),
            'last_seen_at' => $status === 'removed' ? $service->last_seen_at : now(),
            'provider_payload' => $payload ?? $service->provider_payload,
        ])->save();

        $changed = $oldStatus !== $status || (bool) $oldAvailable !== $available;

        if ($changed) {
            Log::info('Provider service status changed', [
                'service_id' => $service->id,
                'provider_id' => $service->provider_id,
                'external_product_id' => $service->external_product_id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'old_available' => $oldAvailable,
                'new_available' => $available,
            ]);
        }

        return $changed;
    }

    public function mapStatus(mixed $raw): string
    {
        if (is_bool($raw)) {
            return $raw ? 'available' : 'unavailable';
        }

        if (is_numeric($raw)) {
            return ((int) $raw) === 1 ? 'available' : 'unavailable';
        }

        $value = Str::of((string) $raw)->lower()->trim()->replace(['-', ' '], '_')->value();

        return match ($value) {
            'available', 'active', 'enabled', 'enable', 'in_stock', 'instock', 'true', 'yes', 'ok' => 'available',
            'unavailable', 'disabled', 'disable', 'stopped', 'stop', 'inactive', 'removed', 'deleted', 'out_of_stock', 'outofstock', 'false', 'no' => 'unavailable',
            default => 'unknown',
        };
    }

    /** @return array{products:array<int, array>} */
    private function fetchCatalog(ApiProvider $provider): array
    {
        $catalogService = $this->manager->forProvider($provider);
        $products = [];
        $page = 1;
        $maxPages = (int) config('services.provider_status_sync.max_pages', 100);

        do {
            $response = $catalogService->browse(['page' => $page, 'page_size' => $provider->catalog_page_size ?: 100]);

            if (! ($response['ok'] ?? false)) {
                throw new \RuntimeException((string) ($response['error_message'] ?? 'فشل جلب الكتالوج من المزود.'));
            }

            $products = array_merge($products, $response['products']);
            $hasNext = (bool) ($response['has_next'] ?? false);
            $page++;
        } while ($hasNext && $page <= $maxPages);

        return ['products' => $products];
    }
}
