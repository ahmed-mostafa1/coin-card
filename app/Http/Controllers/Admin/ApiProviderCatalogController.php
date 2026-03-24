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
        $fetchResult = $this->browseAllPages($catalogService);

        if (! $fetchResult['ok']) {
            return back()->with('error', 'فشل جلب المنتجات: ' . ($fetchResult['error_message'] ?? 'خطأ غير معروف'));
        }

        // Track already-imported external IDs for this provider
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
     * @return array{ok:bool, products:array, count:int, has_next:bool, error_message:?string, was_truncated:bool}
     */
    private function browseAllPages(ApiProviderCatalogService $catalogService): array
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

            $filters = ['page' => $page];
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
}
