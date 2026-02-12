<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MarketCard99CatalogSyncService;
use App\Services\MarketCard99OrderSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MarketCard99IntegrationController extends Controller
{
    public function index(
        MarketCard99CatalogSyncService $catalogSyncService,
        MarketCard99OrderSyncService $orderSyncService
    ): View {
        return view('admin.integrations.marketcard99', [
            'catalogSummary' => $catalogSyncService->lastSummary(),
            'orderSummary' => $orderSyncService->lastSummary(),
        ]);
    }

    public function syncCatalog(MarketCard99CatalogSyncService $catalogSyncService): RedirectResponse
    {
        $result = $catalogSyncService->sync();

        if (!($result['ok'] ?? false)) {
            return redirect()
                ->route('admin.integrations.marketcard99.index')
                ->with('error', $result['message'] ?? 'فشلت مزامنة الكتالوج.')
                ->with('catalog_result', $result);
        }

        return redirect()
            ->route('admin.integrations.marketcard99.index')
            ->with('status', 'تم تنفيذ مزامنة الكتالوج بنجاح.')
            ->with('catalog_result', $result);
    }

    public function syncOrderStatuses(MarketCard99OrderSyncService $orderSyncService): RedirectResponse
    {
        $result = $orderSyncService->sync(request()->user());

        if (!($result['ok'] ?? false)) {
            return redirect()
                ->route('admin.integrations.marketcard99.index')
                ->with('error', $result['message'] ?? 'فشلت مزامنة حالات الطلبات.')
                ->with('orders_result', $result);
        }

        return redirect()
            ->route('admin.integrations.marketcard99.index')
            ->with('status', 'تم تنفيذ مزامنة حالات الطلبات بنجاح.')
            ->with('orders_result', $result);
    }
}

