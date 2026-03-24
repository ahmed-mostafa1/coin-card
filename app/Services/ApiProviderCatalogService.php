<?php

namespace App\Services;

use App\Models\ApiProvider;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Str;

/**
 * Fetches the product catalog from a provider and maps it to our standard shape.
 * Guarantees that 'name' and 'price' are always present after mapping.
 */
class ApiProviderCatalogService
{
    public function __construct(
        private readonly ApiProvider $provider,
        private readonly ApiProviderClient $client
    ) {}

    /**
     * Browse products from the provider.
     *
     * @param  array{page?:int, page_url?:string, search?:string}  $filters
     * @return array{ok:bool, products:array, count:int, has_next:bool, next_page_url:?string, error_message:?string}
     */
    public function browse(array $filters = []): array
    {
        $query = $this->buildCatalogQuery($filters);
        $method = strtolower($this->provider->catalog_method);
        $endpoint = (string) ($filters['page_url'] ?? $this->provider->catalog_endpoint);

        $result = $method === 'post'
            ? $this->client->post($endpoint, $query)
            : $this->client->get($endpoint, $query);

        if (! ($result['ok'] ?? false)) {
            return [
                'ok'            => false,
                'products'      => [],
                'count'         => 0,
                'has_next'      => false,
                'next_page_url' => null,
                'error_message' => $result['error_message'] ?? 'فشل جلب المنتجات من المزود.',
            ];
        }

        $raw = $result['data'];

        $productList = $this->client->resolvePath($raw, $this->provider->catalog_response_path);
        if (! is_array($productList)) {
            $productList = [];
        }

        $count = (int) ($this->client->resolvePath($raw, $this->provider->catalog_count_path) ?? count($productList));
        $nextValue = $this->client->resolvePath($raw, $this->provider->catalog_next_path);
        $hasNext = filled($nextValue);
        $nextPageUrl = is_string($nextValue) && filled($nextValue) ? $nextValue : null;

        $products = [];
        foreach ($productList as $item) {
            try {
                $products[] = $this->mapProduct($item);
            } catch (\Throwable) {
                // Skip malformed items silently
            }
        }

        return [
            'ok'            => true,
            'products'      => $products,
            'count'         => $count,
            'has_next'      => $hasNext,
            'next_page_url' => $nextPageUrl,
            'error_message' => null,
        ];
    }

    /**
     * Map a single raw product from the provider to our standard shape.
     * Throws if required fields (name, price) are missing.
     *
     * @throws \InvalidArgumentException
     */
    public function mapProduct(array $raw): array
    {
        $name  = $this->extract($raw, $this->provider->field_map_name);
        $price = $this->extract($raw, $this->provider->field_map_price);

        if (blank($name)) {
            throw new \InvalidArgumentException('تعذّر استخراج اسم المنتج. تحقق من حقل field_map_name.');
        }
        if (! is_numeric($price)) {
            throw new \InvalidArgumentException('تعذّر استخراج سعر المنتج. تحقق من حقل field_map_price.');
        }

        return [
            'name'        => (string) $name,
            'price'       => (float) $price,
            'external_id' => filled($this->provider->field_map_external_id)
                ? $this->extract($raw, $this->provider->field_map_external_id)
                : null,
            'description' => filled($this->provider->field_map_description)
                ? $this->extract($raw, $this->provider->field_map_description)
                : null,
            'type'        => filled($this->provider->field_map_type)
                ? $this->extract($raw, $this->provider->field_map_type)
                : null,
            'available'   => filled($this->provider->field_map_available)
                ? (bool) $this->extract($raw, $this->provider->field_map_available)
                : true,
            '_raw'        => $raw,
        ];
    }

    /**
     * Import a mapped product into the database.
     * Always sets is_active = false.
     */
    public function import(array $mapped): Service
    {
        $externalId = $mapped['external_id'];

        // Prevent duplicates
        $existing = Service::where('provider_id', $this->provider->id)
            ->where('external_product_id', (string) $externalId)
            ->first();

        if ($existing) {
            throw new \RuntimeException('هذا المنتج مستورد مسبقاً.');
        }

        $category = $this->resolveDefaultCategory();

        $slug = Str::limit(
            ($this->provider->slug) . '-' . ($externalId ?? Str::random(6)) . '-' . Str::slug($mapped['name']),
            190,
            ''
        );

        return Service::create([
            'provider_id'            => $this->provider->id,
            'category_id'            => $category->id,
            'source'                 => $this->provider->slug,
            'name'                   => $mapped['name'],
            'slug'                   => $slug,
            'description'            => $mapped['description'] ?? null,
            'external_product_id'    => $externalId,
            'external_type'          => $mapped['type'] ?? null,
            'provider_price'         => $mapped['price'],
            'provider_is_available'  => $mapped['available'],
            'provider_last_synced_at'=> now(),
            'provider_payload'       => $mapped['_raw'],
            'price'                  => $mapped['price'],
            'is_active'              => false,
            'sync_rule_mode'         => Service::SYNC_RULE_MANUAL,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function extract(array $data, ?string $key): mixed
    {
        if (blank($key)) {
            return null;
        }
        // Support dot-notation for nested fields
        return data_get($data, $key);
    }

    private function buildCatalogQuery(array $filters): array
    {
        if (filled($filters['page_url'] ?? null)) {
            return [];
        }

        $query = [];
        $pageSize = max(1, (int) ($this->provider->catalog_page_size ?: 50));

        if ($this->provider->catalog_pagination_type === ApiProvider::PAGINATION_PAGE_NUMBER) {
            $pageParam = filled($this->provider->catalog_page_param)
                ? $this->provider->catalog_page_param
                : 'page';
            $pageSizeParam = filled($this->provider->catalog_page_size_param)
                ? $this->provider->catalog_page_size_param
                : 'page_size';
            $query[$pageParam]     = (string) ($filters['page'] ?? 1);
            $query[$pageSizeParam] = (string) $pageSize;
        } elseif ($this->provider->catalog_pagination_type === ApiProvider::PAGINATION_OFFSET) {
            $pageParam = filled($this->provider->catalog_page_param)
                ? $this->provider->catalog_page_param
                : 'offset';
            $pageSizeParam = filled($this->provider->catalog_page_size_param)
                ? $this->provider->catalog_page_size_param
                : 'limit';
            $page = (int) ($filters['page'] ?? 1);
            $query[$pageParam]     = (string) (($page - 1) * $pageSize);
            $query[$pageSizeParam] = (string) $pageSize;
        }

        return $query;
    }

    private function resolveDefaultCategory(): Category
    {
        return Category::firstOrCreate(
            ['slug' => $this->provider->slug . '-imported'],
            [
                'name'        => $this->provider->name,
                'source'      => $this->provider->slug,
                'provider_id' => $this->provider->id,
                'is_active'   => false,
                'slug'        => $this->provider->slug . '-imported',
            ]
        );
    }
}
