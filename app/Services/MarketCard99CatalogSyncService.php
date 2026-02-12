<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceFormField;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketCard99CatalogSyncService
{
    private const CACHE_SUMMARY_KEY = 'marketcard99:catalog:last_summary';
    private const LOCK_KEY = 'marketcard99:catalog:sync_lock';

    public function __construct(
        private readonly MarketCard99Client $client
    ) {
    }

    public function sync(): array
    {
        $lock = Cache::lock(self::LOCK_KEY, 900);

        if (!$lock->get()) {
            return [
                'ok' => false,
                'message' => 'عملية مزامنة أخرى قيد التنفيذ حالياً.',
            ];
        }

        $now = now();
        $summary = [
            'ok' => true,
            'started_at' => $now->toDateTimeString(),
            'categories_created' => 0,
            'categories_updated' => 0,
            'services_created' => 0,
            'services_updated' => 0,
            'services_deactivated' => 0,
            'categories_deactivated' => 0,
            'errors' => [],
        ];

        try {
            $categoriesResponse = $this->client->getCategories();
            if (!($categoriesResponse['ok'] ?? false)) {
                return $this->finalizeWithFailure($summary, $categoriesResponse['error_message'] ?? 'فشل جلب التصنيفات');
            }

            $remoteParents = data_get($categoriesResponse['data'], 'data.categories', []);
            if (!is_array($remoteParents)) {
                $remoteParents = [];
            }

            foreach ($remoteParents as $order => $parentData) {
                if (!isset($parentData['id'])) {
                    $summary['errors'][] = 'تم تجاهل تصنيف رئيسي بدون ID.';
                    continue;
                }

                $parentResult = $this->upsertCategory(
                    (int) $parentData['id'],
                    'category',
                    null,
                    $parentData,
                    $order
                );

                $summary[$parentResult['created'] ? 'categories_created' : 'categories_updated']++;

                $subResponse = $this->client->getSubCategories((int) $parentData['id']);
                if (!($subResponse['ok'] ?? false)) {
                    $summary['errors'][] = "فشل جلب التصنيفات الفرعية للتصنيف {$parentData['id']}.";
                    continue;
                }

                $remoteChildren = data_get($subResponse['data'], 'data.categories', []);
                if (!is_array($remoteChildren)) {
                    $remoteChildren = [];
                }

                foreach ($remoteChildren as $childOrder => $childData) {
                    if (!isset($childData['id'])) {
                        $summary['errors'][] = 'تم تجاهل تصنيف فرعي بدون ID.';
                        continue;
                    }

                    $childResult = $this->upsertCategory(
                        (int) $childData['id'],
                        'department',
                        $parentResult['category']->id,
                        $childData,
                        $childOrder
                    );

                    $summary[$childResult['created'] ? 'categories_created' : 'categories_updated']++;

                    $productsResponse = $this->client->getProductsByDepartment((int) $childData['id']);
                    if (!($productsResponse['ok'] ?? false)) {
                        $summary['errors'][] = "فشل جلب المنتجات للقسم {$childData['id']}.";
                        continue;
                    }

                    $products = data_get($productsResponse['data'], 'data.products', []);
                    if (!is_array($products)) {
                        $products = [];
                    }

                    foreach ($products as $productOrder => $product) {
                        if (!isset($product['id'])) {
                            $summary['errors'][] = 'تم تجاهل منتج بدون ID.';
                            continue;
                        }

                        $serviceResult = $this->upsertService(
                            $childResult['category'],
                            $product,
                            $productOrder
                        );

                        $summary[$serviceResult['created'] ? 'services_created' : 'services_updated']++;
                    }
                }
            }

            $summary['categories_deactivated'] = Category::query()
                ->where('source', Category::SOURCE_MARKETCARD99)
                ->whereNotNull('last_seen_at')
                ->where('last_seen_at', '<', $now)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $summary['services_deactivated'] = Service::query()
                ->where('source', Service::SOURCE_MARKETCARD99)
                ->whereNotNull('last_seen_at')
                ->where('last_seen_at', '<', $now)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $summary['finished_at'] = now()->toDateTimeString();
            Cache::put(self::CACHE_SUMMARY_KEY, $summary, now()->addDay());

            return $summary;
        } catch (\Throwable $e) {
            Log::error('MarketCard99: Catalog sync failed', [
                'error' => $e->getMessage(),
            ]);

            return $this->finalizeWithFailure($summary, $e->getMessage());
        } finally {
            optional($lock)->release();
        }
    }

    public function lastSummary(): ?array
    {
        $summary = Cache::get(self::CACHE_SUMMARY_KEY);

        return is_array($summary) ? $summary : null;
    }

    private function finalizeWithFailure(array $summary, string $message): array
    {
        $summary['ok'] = false;
        $summary['message'] = $message;
        $summary['errors'][] = $message;
        $summary['finished_at'] = now()->toDateTimeString();

        Cache::put(self::CACHE_SUMMARY_KEY, $summary, now()->addDay());

        return $summary;
    }

    /**
     * @return array{category:Category,created:bool}
     */
    private function upsertCategory(
        int $externalId,
        string $externalType,
        ?int $parentId,
        array $remote,
        int $sortOrder
    ): array {
        $category = Category::query()
            ->where('source', Category::SOURCE_MARKETCARD99)
            ->where('external_type', $externalType)
            ->where('external_id', $externalId)
            ->first();

        $isCreated = !$category;
        $slugPrefix = $externalType === 'category' ? 'mc99-cat-' : 'mc99-dept-';
        $data = [
            'parent_id' => $parentId,
            'source' => Category::SOURCE_MARKETCARD99,
            'external_type' => $externalType,
            'external_id' => $externalId,
            'name' => (string) ($remote['name'] ?? "قسم {$externalId}"),
            'slug' => $slugPrefix.$externalId,
            'is_active' => (bool) ($remote['is_available'] ?? true),
            'sort_order' => $sortOrder,
            'last_seen_at' => now(),
        ];

        $imagePath = $this->storeRemoteImage($remote['img'] ?? null, 'marketcard99/categories');
        if ($imagePath !== null) {
            $data['image_path'] = $imagePath;
        }

        if ($category) {
            $category->fill($data)->save();
        } else {
            $category = Category::create($data);
        }

        return [
            'category' => $category,
            'created' => $isCreated,
        ];
    }

    /**
     * @return array{service:Service,created:bool}
     */
    private function upsertService(Category $category, array $product, int $sortOrder): array
    {
        $externalProductId = (int) $product['id'];
        $service = Service::query()
            ->where('source', Service::SOURCE_MARKETCARD99)
            ->where('external_product_id', $externalProductId)
            ->first();

        $isCreated = !$service;
        $providerPrice = $this->normalizeDecimal($product['price'] ?? null);
        $providerUnitPrice = $this->normalizeDecimal($product['unit_price'] ?? null);
        $initialPrice = $providerPrice > 0 ? $providerPrice : $providerUnitPrice;
        $providerIsAvailable = (bool) ($product['is_available'] ?? true);

        $data = [
            'category_id' => $category->id,
            'source' => Service::SOURCE_MARKETCARD99,
            'name' => (string) ($product['name'] ?? "منتج {$externalProductId}"),
            'slug' => 'mc99-svc-'.$externalProductId,
            'description' => isset($product['info']) ? (string) $product['info'] : null,
            'external_product_id' => $externalProductId,
            'external_type' => isset($product['type']) ? (string) $product['type'] : null,
            'provider_payload' => $product,
            'provider_price' => $providerPrice > 0 ? $providerPrice : null,
            'provider_unit_price' => $providerUnitPrice > 0 ? $providerUnitPrice : null,
            'provider_is_available' => $providerIsAvailable,
            'provider_last_synced_at' => now(),
            'sort_order' => $sortOrder,
            'last_seen_at' => now(),
        ];

        $imagePath = $this->storeRemoteImage($product['img'] ?? null, 'marketcard99/services');
        if ($imagePath !== null) {
            $data['image_path'] = $imagePath;
        }

        if ($service) {
            if ($service->sync_rule_mode === Service::SYNC_RULE_AUTO || blank($service->sync_rule_mode)) {
                $data = array_merge($data, $this->deriveSyncRules($product, $service->requires_purchase_password));
            }
            $data['is_active'] = $providerIsAvailable && $service->price > 0;
            $service->fill($data)->save();
        } else {
            $derivedRules = $this->deriveSyncRules($product, false);
            $service = Service::create(array_merge($data, $derivedRules, [
                'price' => $initialPrice > 0 ? $initialPrice : 0,
                'is_active' => $providerIsAvailable && $initialPrice > 0,
                'sync_rule_mode' => Service::SYNC_RULE_AUTO,
            ]));
        }

        if ($service->sync_rule_mode === Service::SYNC_RULE_AUTO) {
            $this->syncReservedFields($service);
        }

        return [
            'service' => $service,
            'created' => $isCreated,
        ];
    }

    private function deriveSyncRules(array $product, bool $requiresPurchasePasswordOverride): array
    {
        $type = strtolower((string) ($product['type'] ?? ''));
        $isFree = (bool) ($product['is_free'] ?? false);
        $minQty = (float) ($product['min_qty'] ?? 0);
        $maxQty = (float) ($product['max_qty'] ?? 0);

        return [
            'requires_customer_id' => in_array($type, ['id', 'account'], true),
            'requires_amount' => $isFree || $minQty > 0 || $maxQty > 0,
            'supports_purchase_password' => $isFree,
            'requires_purchase_password' => $isFree ? $requiresPurchasePasswordOverride : false,
            'sync_rule_mode' => Service::SYNC_RULE_AUTO,
        ];
    }

    private function syncReservedFields(Service $service): void
    {
        $reserved = [
            'customer_identifier' => [
                'enabled' => $service->requires_customer_id,
                'required' => $service->requires_customer_id,
                'label' => 'معرف المستخدم',
                'placeholder' => 'أدخل معرف المستخدم',
                'sort_order' => 900,
            ],
            'external_amount' => [
                'enabled' => $service->requires_amount,
                'required' => $service->requires_amount,
                'label' => 'المبلغ',
                'placeholder' => 'أدخل المبلغ المطلوب',
                'sort_order' => 901,
            ],
            'purchase_password' => [
                'enabled' => $service->supports_purchase_password,
                'required' => $service->requires_purchase_password,
                'label' => 'كلمة سر الشراء',
                'placeholder' => 'أدخل كلمة سر الشراء',
                'sort_order' => 902,
            ],
        ];

        foreach ($reserved as $nameKey => $meta) {
            if (!$meta['enabled']) {
                ServiceFormField::query()
                    ->where('service_id', $service->id)
                    ->where('name_key', $nameKey)
                    ->delete();
                continue;
            }

            ServiceFormField::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'name_key' => $nameKey,
                ],
                [
                    'type' => ServiceFormField::TYPE_TEXT,
                    'label' => $meta['label'],
                    'label_en' => Str::title(str_replace('_', ' ', $nameKey)),
                    'placeholder' => $meta['placeholder'],
                    'placeholder_en' => Str::title(str_replace('_', ' ', $nameKey)),
                    'is_required' => $meta['required'],
                    'sort_order' => $meta['sort_order'],
                ]
            );
        }
    }

    private function normalizeDecimal(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return round((float) $value, 4);
    }

    private function storeRemoteImage(mixed $url, string $directory): ?string
    {
        if (!is_string($url) || blank($url)) {
            return null;
        }

        try {
            $response = Http::timeout(20)->get($url);
            if (!$response->successful()) {
                return null;
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
            $extension = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)
                ? strtolower($extension)
                : 'jpg';
            $path = $directory.'/'.Str::uuid().'.'.$extension;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $e) {
            Log::warning('MarketCard99: failed downloading image', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
