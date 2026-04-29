<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\Service;
use App\Services\ApiProviderCatalogService;
use App\Services\ApiProviderManager;
use App\Services\DailyCardClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiProviderCatalogController extends Controller
{
    private const MAX_FULL_CATALOG_PAGES = 50;
    private const DAILYCARD_REMOTE_PAGE_SIZE = 500;
    private const DAILYCARD_LOCAL_PAGE_SIZE = 20;

    public function __construct(
        private readonly ApiProviderManager $manager,
        private readonly DailyCardClient $dailyCardClient
    ) {}

    public function index(Request $request, ApiProvider $provider): View|RedirectResponse
    {
        if (! $provider->is_active) {
            return redirect()->route('admin.providers.index')
                ->with('error', 'المزود غير مفعّل. فعّله أولاً من إدارة المزودين.');
        }

        $mode = $this->resolveMode($request, $provider);
        $filters = $this->catalogFilters($request, $provider, $mode);

        Log::info('Provider catalog index hit', [
            'provider_id' => $provider->id,
            'provider_slug' => $provider->slug,
            'mode' => $mode,
            'filters' => $filters,
            'is_dailycard' => $this->isDailyCard($provider),
            'request_url' => $request->fullUrl(),
        ]);

        $fetchResult = $this->isDailyCard($provider)
            ? $this->browseDailyCardCatalog($filters, $mode)
            : $this->browseGenericCatalog($provider, $filters, $mode);

        if (! $fetchResult['ok']) {
            return back()->with('error', 'فشل جلب المنتجات: ' . ($fetchResult['error_message'] ?? 'خطأ غير معروف'));
        }

        $importedIdsQuery = Service::query()
            ->whereNotNull('external_product_id');

        if ($this->isDailyCard($provider)) {
            $importedIdsQuery->where(function ($query) use ($provider): void {
                $query->where('provider_id', $provider->id)
                    ->orWhere('source', 'dailycard');
            });
        } else {
            $importedIdsQuery->where('provider_id', $provider->id);
        }

        $importedIds = $importedIdsQuery
            ->pluck('id', 'external_product_id')
            ->toArray();

        return view('admin.providers.catalog', [
            'provider' => $provider,
            'products' => $fetchResult['products'],
            'count' => $fetchResult['count'],
            'importedIds' => $importedIds,
            'wasTruncated' => $fetchResult['was_truncated'] ?? false,
            'mode' => $mode,
            'search' => $filters['search'] ?? '',
            'categoryFilter' => $filters['category'] ?? '',
            'productTypeFilter' => $filters['product_type'] ?? '',
            'currentPage' => $fetchResult['current_page'] ?? 1,
            'totalPages' => $fetchResult['total_pages'] ?? null,
            'hasPreviousPage' => $fetchResult['has_previous'] ?? false,
            'hasNextPage' => $fetchResult['has_next'] ?? false,
            'isDailyCard' => $this->isDailyCard($provider),
            'searchIsLocal' => $fetchResult['search_is_local'] ?? false,
        ]);
    }

    public function import(Request $request, ApiProvider $provider): RedirectResponse
    {
        $request->validate([
            'product_data' => ['required', 'json'],
        ]);

        $mapped = json_decode($request->input('product_data'), true);
        $admin = $request->user();

        Log::info('Provider catalog import requested', [
            'provider_id' => $provider->id,
            'provider_slug' => $provider->slug,
            'admin_id' => $admin?->id,
            'external_product_id' => $mapped['external_id'] ?? null,
            'product_name' => $mapped['name'] ?? null,
            'product_type' => $mapped['type'] ?? null,
            'price' => $mapped['price'] ?? null,
        ]);

        try {
            $catalogService = $this->manager->forProvider($provider);
            $service = $catalogService->import($mapped);

            Log::info('Provider catalog import succeeded', [
                'provider_id' => $provider->id,
                'provider_slug' => $provider->slug,
                'admin_id' => $admin?->id,
                'service_id' => $service->id,
                'external_product_id' => $service->external_product_id,
                'service_name' => $service->name,
            ]);

            return back()->with(
                'success',
                'تم استيراد "' . $service->name . '" بنجاح. يمكنك الآن تعديل سعره وتفعيله من صفحة الخدمات.'
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'فشل الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool, search_is_local:bool}
     */
    private function browseGenericCatalog(ApiProvider $provider, array $filters, string $mode): array
    {
        $catalogService = $this->manager->forProvider($provider);

        return $mode === 'page'
            ? $this->browseSinglePage($provider, $catalogService, $filters)
            : $this->browseAllPages($catalogService, $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool, search_is_local:bool}
     */
    private function browseDailyCardCatalog(array $filters, string $mode): array
    {
        if (! $this->dailyCardClient->isEnabled()) {
            return [
                'ok' => false,
                'products' => [],
                'count' => 0,
                'has_next' => false,
                'error_message' => 'خدمة DailyCard غير مفعّلة أو بياناتها غير مكتملة في متغيرات البيئة.',
                'was_truncated' => false,
                'current_page' => 1,
                'total_pages' => 1,
                'has_previous' => false,
                'search_is_local' => true,
            ];
        }

        $remoteResult = $this->fetchAllDailyCardProducts($filters);
        if (! $remoteResult['ok']) {
            return $remoteResult;
        }

        $products = $this->applyDailyCardLocalSearch($remoteResult['products'], $filters['search'] ?? null);
        $totalCount = count($products);

        if ($mode === 'all') {
            return [
                'ok' => true,
                'products' => $products,
                'count' => $totalCount,
                'has_next' => false,
                'error_message' => null,
                'was_truncated' => $remoteResult['was_truncated'],
                'current_page' => 1,
                'total_pages' => 1,
                'has_previous' => false,
                'search_is_local' => true,
            ];
        }

        $currentPage = max(1, (int) ($filters['page'] ?? 1));
        $totalPages = max(1, (int) ceil(max(1, $totalCount) / self::DAILYCARD_LOCAL_PAGE_SIZE));
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * self::DAILYCARD_LOCAL_PAGE_SIZE;
        $pageProducts = array_slice($products, $offset, self::DAILYCARD_LOCAL_PAGE_SIZE);

        Log::info('DailyCard catalog page slice', [
            'mode' => $mode,
            'requested_page' => (int) ($filters['page'] ?? 1),
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
            'offset' => $offset,
            'returned_count' => count($pageProducts),
            'first_external_id' => $pageProducts[0]['external_id'] ?? null,
            'search' => $filters['search'] ?? null,
            'category' => $filters['category'] ?? null,
            'product_type' => $filters['product_type'] ?? null,
        ]);

        return [
            'ok' => true,
            'products' => $pageProducts,
            'count' => $totalCount,
            'has_next' => $currentPage < $totalPages,
            'error_message' => null,
            'was_truncated' => $remoteResult['was_truncated'],
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'has_previous' => $currentPage > 1,
            'search_is_local' => true,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool, search_is_local:bool}
     */
    private function fetchAllDailyCardProducts(array $filters): array
    {
        $products = [];
        $count = 0;
        $page = 1;
        $hasNext = false;
        $wasTruncated = false;
        $seenProducts = [];

        do {
            $remoteFilters = [
                'page' => $page,
                'page_size' => self::DAILYCARD_REMOTE_PAGE_SIZE,
            ];

            if (filled($filters['category'] ?? null)) {
                $remoteFilters['category'] = (int) $filters['category'];
            }

            if (filled($filters['product_type'] ?? null)) {
                $remoteFilters['product_type'] = (string) $filters['product_type'];
            }

            $result = $this->dailyCardClient->getProducts($remoteFilters);

            if (! ($result['ok'] ?? false)) {
                return [
                    'ok' => false,
                    'products' => [],
                    'count' => 0,
                    'has_next' => false,
                    'error_message' => $result['error_message'] ?? null,
                    'was_truncated' => false,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'has_previous' => false,
                    'search_is_local' => true,
                ];
            }

            $data = $result['data'] ?? [];
            $pageProducts = is_array($data['results'] ?? null) ? $data['results'] : [];
            $newProductsOnPage = 0;

            foreach ($pageProducts as $product) {
                if (! is_array($product)) {
                    continue;
                }

                $key = 'dailycard:' . ($product['id'] ?? sha1(json_encode($product)));
                if (isset($seenProducts[$key])) {
                    continue;
                }

                $seenProducts[$key] = true;
                $products[] = $this->mapDailyCardProduct($product);
                $newProductsOnPage++;
            }

            $count = max($count, (int) ($data['count'] ?? 0), count($products));
            $hasNext = ! empty($data['next']);

            if ($hasNext && $newProductsOnPage === 0) {
                $wasTruncated = true;
                break;
            }

            $page++;
        } while ($hasNext && $page <= self::MAX_FULL_CATALOG_PAGES);

        if ($hasNext && $page > self::MAX_FULL_CATALOG_PAGES) {
            $wasTruncated = true;
        }

        return [
            'ok' => true,
            'products' => $products,
            'count' => $count,
            'has_next' => false,
            'error_message' => null,
            'was_truncated' => $wasTruncated,
            'current_page' => 1,
            'total_pages' => 1,
            'has_previous' => false,
            'search_is_local' => true,
        ];
    }

    /**
     * @param  array<string, mixed>  $baseFilters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool, search_is_local:bool}
     */
    private function browseAllPages($catalogService, array $baseFilters = []): array
    {
        $products = [];
        $seenProducts = [];
        $seenRequests = [];
        $count = 0;
        $page = 1;
        $hasNext = false;
        $nextPageUrl = null;
        $wasTruncated = false;

        do {
            $requestKey = $nextPageUrl ? 'url:' . $nextPageUrl : 'page:' . $page;

            if (isset($seenRequests[$requestKey])) {
                $wasTruncated = true;
                break;
            }

            $seenRequests[$requestKey] = true;

            $filters = [...$baseFilters, 'page' => $page];
            if ($nextPageUrl) {
                $filters['page_url'] = $nextPageUrl;
            }

            $result = $catalogService->browse($filters);

            if (! $result['ok']) {
                return [
                    'ok' => false,
                    'products' => [],
                    'count' => 0,
                    'has_next' => false,
                    'error_message' => $result['error_message'] ?? null,
                    'was_truncated' => false,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'has_previous' => false,
                    'search_is_local' => false,
                ];
            }

            $newProductsOnPage = 0;

            foreach ($result['products'] as $product) {
                $productKey = $this->productKey($product);

                if (isset($seenProducts[$productKey])) {
                    continue;
                }

                $seenProducts[$productKey] = true;
                $products[] = $product;
                $newProductsOnPage++;
            }

            $count = max($count, (int) $result['count'], count($products));
            $hasNext = (bool) $result['has_next'];
            $nextPageUrl = $result['next_page_url'] ?? null;

            if ($hasNext && $newProductsOnPage === 0) {
                $wasTruncated = true;
                break;
            }

            $page++;
        } while ($hasNext && $page <= self::MAX_FULL_CATALOG_PAGES);

        if ($hasNext && $page > self::MAX_FULL_CATALOG_PAGES) {
            $wasTruncated = true;
        }

        return [
            'ok' => true,
            'products' => $products,
            'count' => $count,
            'has_next' => false,
            'error_message' => null,
            'was_truncated' => $wasTruncated,
            'current_page' => 1,
            'total_pages' => 1,
            'has_previous' => false,
            'search_is_local' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool, search_is_local:bool}
     */
    private function browseSinglePage(ApiProvider $provider, $catalogService, array $filters): array
    {
        $currentPage = max(1, (int) ($filters['page'] ?? 1));
        $result = $catalogService->browse($filters);

        if (! $result['ok']) {
            return [
                'ok' => false,
                'products' => [],
                'count' => 0,
                'has_next' => false,
                'error_message' => $result['error_message'] ?? null,
                'was_truncated' => false,
                'current_page' => $currentPage,
                'total_pages' => 1,
                'has_previous' => $currentPage > 1,
                'search_is_local' => false,
            ];
        }

        $pageSize = max(1, (int) ($filters['page_size'] ?? $provider->catalog_page_size ?: 50));
        $totalCount = max((int) ($result['count'] ?? 0), count($result['products']));
        $totalPages = max(1, (int) ceil($totalCount / $pageSize));
        $hasNext = (bool) $result['has_next'] || $currentPage < $totalPages;

        return [
            'ok' => true,
            'products' => $result['products'],
            'count' => $totalCount,
            'has_next' => $hasNext,
            'error_message' => null,
            'was_truncated' => false,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'has_previous' => $currentPage > 1,
            'search_is_local' => false,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, array<string, mixed>>
     */
    private function applyDailyCardLocalSearch(array $products, ?string $search): array
    {
        $term = Str::lower(trim((string) $search));
        if ($term === '') {
            return $products;
        }

        return array_values(array_filter($products, function (array $product) use ($term): bool {
            $haystacks = [
                Str::lower((string) ($product['name'] ?? '')),
                Str::lower((string) ($product['description'] ?? '')),
                Str::lower((string) ($product['type'] ?? '')),
            ];

            foreach ($haystacks as $haystack) {
                if ($haystack !== '' && str_contains($haystack, $term)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function mapDailyCardProduct(array $raw): array
    {
        return [
            'name' => (string) ($raw['name'] ?? ''),
            'price' => (float) ($raw['price'] ?? 0),
            'external_id' => $raw['id'] ?? null,
            'description' => isset($raw['description']) ? (string) $raw['description'] : null,
            'type' => isset($raw['product_type']) ? (string) $raw['product_type'] : null,
            'available' => (bool) ($raw['available'] ?? true),
            '_raw' => $raw,
        ];
    }

    private function productKey(array $product): string
    {
        $externalId = trim((string) ($product['external_id'] ?? ''));
        if ($externalId !== '') {
            return 'external:' . $externalId;
        }

        $raw = $product['_raw'] ?? $product;
        $encoded = json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return 'raw:' . sha1($encoded !== false ? $encoded : serialize($raw));
    }

    private function resolveMode(Request $request, ApiProvider $provider): string
    {
        $mode = strtolower(trim((string) $request->query('mode', '')));

        if (in_array($mode, ['all', 'page'], true)) {
            return $mode;
        }

        return $this->isDailyCard($provider) ? 'page' : 'page';
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogFilters(Request $request, ApiProvider $provider, string $mode): array
    {
        $filters = [
            'page' => max(1, (int) $request->integer('page', 1)),
            'page_size' => $mode === 'all'
                ? 5000
                : max(1, (int) ($provider->catalog_page_size ?: 50)),
        ];

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $filters['search'] = $search;
        }

        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $filters['category'] = $category;
        }

        $productType = trim((string) $request->query('product_type', ''));
        if ($productType !== '') {
            $filters['product_type'] = $productType;
        }

        return $filters;
    }

    private function isDailyCard(ApiProvider $provider): bool
    {
        return $this->normalizedProviderSlug($provider) === 'dailycard';
    }

    private function normalizedProviderSlug(ApiProvider $provider): string
    {
        return Str::of((string) $provider->slug)
            ->lower()
            ->replace(['-', '_', ' '], '')
            ->value();
    }
}
