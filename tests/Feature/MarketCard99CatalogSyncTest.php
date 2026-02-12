<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceFormField;
use App\Services\MarketCard99CatalogSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketCard99CatalogSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.marketcard99.base_url', 'https://app.market-card99.com');
        config()->set('services.marketcard99.token', 'testing-token');
    }

    public function test_catalog_sync_creates_records_and_keeps_manual_price_override(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name' => 'Existing',
            'slug' => 'existing-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'source' => Service::SOURCE_MARKETCARD99,
            'name' => 'Old Name',
            'slug' => 'mc99-svc-100',
            'price' => 99,
            'is_active' => true,
            'external_product_id' => 100,
            'sync_rule_mode' => Service::SYNC_RULE_AUTO,
        ]);

        Http::fake([
            'https://app.market-card99.com/api/v2/categories' => Http::response([
                'data' => [
                    'categories' => [
                        ['id' => 1, 'name' => 'Apps', 'is_available' => true, 'img' => 'https://cdn.test/cat.jpg'],
                    ],
                ],
            ], 200),
            'https://app.market-card99.com/api/v2/categories/1' => Http::response([
                'data' => [
                    'categories' => [
                        ['id' => 10, 'name' => 'Chat', 'is_available' => true, 'img' => 'https://cdn.test/dep.jpg'],
                    ],
                ],
            ], 200),
            'https://app.market-card99.com/api/v2/departments/10' => Http::response([
                'data' => [
                    'products' => [
                        [
                            'id' => 100,
                            'name' => 'Waha Product',
                            'type' => 'id',
                            'info' => 'Provider description',
                            'img' => 'https://cdn.test/prod.jpg',
                            'is_free' => false,
                            'min_qty' => 0,
                            'max_qty' => 0,
                            'is_available' => true,
                            'price' => 15,
                            'unit_price' => 0,
                        ],
                    ],
                ],
            ], 200),
            'https://cdn.test/*' => Http::response('fake-image', 200),
        ]);

        $result = app(MarketCard99CatalogSyncService::class)->sync();
        $service->refresh();

        $this->assertTrue($result['ok']);
        $this->assertSame(99.0, (float) $service->price);
        $this->assertSame(15.0, (float) $service->provider_price);
        $this->assertSame(Service::SOURCE_MARKETCARD99, $service->source);
        $this->assertNotNull($service->image_path);
        Storage::disk('public')->assertExists($service->image_path);

        $this->assertDatabaseHas('categories', [
            'source' => Category::SOURCE_MARKETCARD99,
            'external_type' => 'category',
            'external_id' => 1,
        ]);

        $this->assertDatabaseHas('service_form_fields', [
            'service_id' => $service->id,
            'name_key' => 'customer_identifier',
        ]);
    }

    public function test_catalog_sync_deactivates_missing_synced_records(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'source' => Category::SOURCE_MARKETCARD99,
            'external_type' => 'category',
            'external_id' => 1,
            'name' => 'Old Parent',
            'slug' => 'mc99-cat-1',
            'is_active' => true,
            'last_seen_at' => now()->subDay(),
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'source' => Service::SOURCE_MARKETCARD99,
            'name' => 'Old Product',
            'slug' => 'mc99-svc-500',
            'price' => 20,
            'is_active' => true,
            'external_product_id' => 500,
            'last_seen_at' => now()->subDay(),
        ]);

        Http::fake([
            'https://app.market-card99.com/api/v2/categories' => Http::response([
                'data' => ['categories' => []],
            ], 200),
        ]);

        $result = app(MarketCard99CatalogSyncService::class)->sync();

        $category->refresh();
        $service->refresh();

        $this->assertTrue($result['ok']);
        $this->assertFalse($category->is_active);
        $this->assertFalse($service->is_active);
        $this->assertSame(0, ServiceFormField::query()->count());
    }
}

