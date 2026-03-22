<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Services\DailyCardClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DailyCardCatalogController extends Controller
{
    public function __construct(
        private readonly DailyCardClient $client
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        if (! $this->client->isEnabled()) {
            return redirect()->route('admin.index')
                ->with('error', 'خدمة DailyCard غير مفعّلة. تحقق من متغيرات البيئة DAILYCARD_API_KEY و DAILYCARD_SECRET.');
        }

        // NOTE: Do NOT pass 'search' to DailyCard — their backend has a bug
        // with LIKE queries using Python % formatting. Filter client-side instead.
        $filters = [
            'page'      => (string) $request->input('page', 1),
            'page_size' => '500',
        ];

        if ($request->filled('product_type')) {
            $filters['product_type'] = (string) $request->input('product_type');
        }

        $result = $this->client->getProducts($filters);

        if (! ($result['ok'] ?? false)) {
            return back()->with('error', 'فشل جلب المنتجات: ' . ($result['error_message'] ?? 'خطأ غير معروف'));
        }

        $data     = $result['data'] ?? [];
        $products = $data['results'] ?? [];
        $count    = $data['count'] ?? 0;
        $hasNext  = ! empty($data['next']);

        $importedIds = Service::where('source', Service::SOURCE_DAILYCARD)
            ->whereNotNull('external_product_id')
            ->pluck('id', 'external_product_id')
            ->toArray();

        return view('admin.dailycard.index', compact(
            'products',
            'count',
            'hasNext',
            'importedIds',
        ));
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'   => ['required', 'integer'],
            'name'         => ['required', 'string', 'max:255'],
            'price'        => ['required', 'numeric', 'min:0'],
            'product_type' => ['nullable', 'string', 'max:100'],
            'available'    => ['nullable', 'boolean'],
            'min_qty'      => ['nullable', 'integer', 'min:1'],
            'max_qty'      => ['nullable', 'integer'],
        ]);

        $externalId = (int) $validated['product_id'];

        // Already imported?
        $existing = Service::where('source', Service::SOURCE_DAILYCARD)
            ->where('external_product_id', $externalId)
            ->first();

        if ($existing) {
            return back()->with('error', 'هذا المنتج مستورد مسبقاً.');
        }

        // Place under default "DailyCard" category or let admin assign later
        $category = Category::firstOrCreate(
            ['slug' => 'dailycard-imported'],
            [
                'name'     => 'DailyCard',
                'source'   => Category::SOURCE_DAILYCARD,
                'is_active'=> false,
                'slug'     => 'dailycard-imported',
            ]
        );

        $providerPrice = (float) $validated['price'];
        $slug = 'dc-' . $externalId . '-' . Str::slug($validated['name'], '-');

        Service::create([
            'category_id'            => $category->id,
            'source'                 => Service::SOURCE_DAILYCARD,
            'name'                   => $validated['name'],
            'slug'                   => Str::limit($slug, 190, ''),
            'external_product_id'    => $externalId,
            'external_type'          => $validated['product_type'] ?? null,
            'provider_price'         => $providerPrice,
            'provider_is_available'  => (bool) ($validated['available'] ?? true),
            'provider_last_synced_at'=> now(),
            'min_quantity'           => $validated['min_qty'] ?? null,
            'max_quantity'           => $validated['max_qty'] ?? null,
            'price'                  => $providerPrice, // admin will adjust
            'is_active'              => false,           // pending admin activation
            'sync_rule_mode'         => Service::SYNC_RULE_MANUAL,
            'provider_payload'       => $validated,
        ]);

        return back()->with('success', 'تم استيراد المنتج "' . $validated['name'] . '" بنجاح. يمكنك الآن تعديل سعره وتفعيله من صفحة الخدمات.');
    }
}
