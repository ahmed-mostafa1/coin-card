<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\Service;
use App\Services\ApiProviderCatalogService;
use App\Services\ApiProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiProviderCatalogController extends Controller
{
    private const MAX_FULL_CATALOG_PAGES = 50;
    private const DAILYCARD_FULL_FETCH_PAGE_SIZE = 5000;

    public function __construct(
        private readonly ApiProviderManager $manager
    ) {}

    public function index(Request $request, ApiProvider $provider): View|RedirectResponse
    {
        if (! $provider->is_active) {
            return redirect()->route('admin.providers.index')
                ->with('error', 'المزود غير مفعّل. فعّله أولاً من إدارة المزودين.');
        }

        $catalogService = $this->manager->forProvider($provider);
        $mode = $this->resolveMode($request, $provider);
        $filters = $this->catalogFilters($request, $provider, $mode);

        $fetchResult = $mode === 'page'
            ? $this->browseSinglePage($provider, $catalogService, $filters)
            : $this->browseAllPages($catalogService, $filters);

        if (! $fetchResult['ok']) {
            return back()->with('error', 'فشل جلب المنتجات: ' . ($fetchResult['error_message'] ?? 'خطأ غير معروف'));
        }

        $importedIds = Service::where('provider_id', $provider->id)
            ->whereNotNull('external_product_id')
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
        ]);
    }

    public function import(Request $request, ApiProvider $provider): RedirectResponse
    {
        $request->validate([
            'product_data' => ['required', 'json'],
        ]);

        $mapped = json_decode($request->input('product_data'), true);

        try {
            $catalogService = $this->manager->forProvider($provider);
            $service = $catalogService->import($mapped);

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
     * @param  array<string, mixed>  $baseFilters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool}
     */
    private function browseAllPages(ApiProviderCatalogService $catalogService, array $baseFilters = []): array
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
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool, current_page:int, total_pages:int, has_previous:bool}
     */
    private function browseSinglePage(ApiProvider $provider, ApiProviderCatalogService $catalogService, array $filters): array
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

        return $this->isDailyCard($provider) ? 'all' : 'page';
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogFilters(Request $request, ApiProvider $provider, string $mode): array
    {
        $filters = [
            'page' => max(1, (int) $request->integer('page', 1)),
            'page_size' => $mode === 'all'
                ? ($this->optimizedFullFetchPageSize($provider) ?? max(1, (int) ($provider->catalog_page_size ?: 50)))
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

    private function optimizedFullFetchPageSize(ApiProvider $provider): ?int
    {
        return $this->isDailyCard($provider) ? self::DAILYCARD_FULL_FETCH_PAGE_SIZE : null;
    }

    private function isDailyCard(ApiProvider $provider): bool
    {
        return $provider->slug === 'dailycard';
    }
}
