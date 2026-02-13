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

    public function test_catalog_sync_creates_only_new_records_without_overriding_existing_records(): void
    {
        Storage::fake('public');

        $parent = Category::create([
            'source' => Category::SOURCE_MARKETCARD99,
            'external_type' => 'category',
            'external_id' => 1,
            'name' => 'Apps Existing',
            'slug' => 'mc99-cat-1',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $child = Category::create([
            'parent_id' => $parent->id,
            'source' => Category::SOURCE_MARKETCARD99,
            'external_type' => 'department',
            'external_id' => 10,
            'name' => 'Chat Existing',
            'slug' => 'mc99-dept-10',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $existingService = Service::create([
            'category_id' => $child->id,
            'source' => Service::SOURCE_MARKETCARD99,
            'name' => 'Old Name',
            'slug' => 'mc99-svc-100',
            'price' => 99,
            'is_active' => true,
            'external_product_id' => 100,
            'provider_price' => null,
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
                            'name' => 'Waha Product Existing',
                            'type' => 'id',
                            'info' => 'Provider description changed',
                            'img' => 'https://cdn.test/prod-existing.jpg',
                            'is_free' => false,
                            'min_qty' => 0,
                            'max_qty' => 0,
                            'is_available' => true,
                            'price' => 15,
                            'unit_price' => 0,
                        ],
                        [
                            'id' => 101,
                            'name' => 'Waha Product New',
                            'type' => 'id',
                            'info' => 'New product description',
                            'img' => 'https://cdn.test/prod-new.jpg',
                            'is_free' => false,
                            'min_qty' => 0,
                            'max_qty' => 0,
                            'is_available' => true,
                            'price' => 25,
                            'unit_price' => 0,
                        ],
                    ],
                ],
            ], 200),
            'https://cdn.test/*' => Http::response('fake-image', 200),
        ]);

        $result = app(MarketCard99CatalogSyncService::class)->sync();

        $existingService->refresh();
        $newService = Service::query()->where('external_product_id', 101)->first();

        $this->assertTrue($result['ok']);
        $this->assertSame(0, $result['categories_created']);
        $this->assertSame(2, $result['categories_skipped']);
        $this->assertSame(1, $result['services_created']);
        $this->assertSame(1, $result['services_skipped']);

        // Existing service should remain untouched.
        $this->assertSame('Old Name', $existingService->name);
        $this->assertSame(99.0, (float) $existingService->price);
        $this->assertNull($existingService->provider_price);

        // No duplicate for existing external product.
        $this->assertSame(1, Service::query()->where('external_product_id', 100)->count());

        // New service should be created with synced data.
        $this->assertNotNull($newService);
        $this->assertSame('Waha Product New', $newService->name);
        $this->assertSame(25.0, (float) $newService->price);
        $this->assertSame(25.0, (float) $newService->provider_price);
        $this->assertNotNull($newService->image_path);
        Storage::disk('public')->assertExists($newService->image_path);

        $this->assertDatabaseHas('service_form_fields', [
            'service_id' => $newService->id,
            'name_key' => 'customer_identifier',
        ]);
    }

    public function test_catalog_sync_does_not_deactivate_missing_synced_records_in_create_only_mode(): void
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
        $this->assertTrue($category->is_active);
        $this->assertTrue($service->is_active);
        $this->assertSame(0, $result['categories_deactivated']);
        $this->assertSame(0, $result['services_deactivated']);
        $this->assertSame(0, ServiceFormField::query()->count());
    }
}
