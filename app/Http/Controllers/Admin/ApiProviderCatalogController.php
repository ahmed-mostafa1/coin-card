<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\Service;
use App\Services\ApiProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiProviderCatalogController extends Controller
{
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
        $page = (int) $request->input('page', 1);
        $result = $catalogService->browse(['page' => $page]);

        if (! $result['ok']) {
            return back()->with('error', 'فشل جلب المنتجات: ' . ($result['error_message'] ?? 'خطأ غير معروف'));
        }

        // Track already-imported external IDs for this provider
        $importedIds = Service::where('provider_id', $provider->id)
            ->whereNotNull('external_product_id')
            ->pluck('id', 'external_product_id')
            ->toArray();

        return view('admin.providers.catalog', [
            'provider'    => $provider,
            'products'    => $result['products'],
            'count'       => $result['count'],
            'hasNext'     => $result['has_next'],
            'currentPage' => $page,
            'importedIds' => $importedIds,
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
}
