<?php

namespace Tests\Feature;

use App\Models\ApiProvider;
use App\Models\User;
use App\Services\ApiProviderCatalogService;
use App\Services\ApiProviderClient;
use App\Services\ApiProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProviderCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_dailycard_legacy_route_redirects_to_provider_catalog(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeProvider();

        $this->actingAs($admin)
            ->get('/admin/dailycard')
            ->assertRedirect(route('admin.providers.catalog.index', $provider));
    }

    public function test_provider_catalog_renders_all_pages_on_one_screen_for_any_provider(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with(['page' => 1])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Product One', 'type' => 'stock', 'price' => 10, 'available' => true],
            ],
            'count' => 2,
            'has_next' => true,
            'next_page_url' => null,
            'error_message' => null,
        ]);
        $catalogService->shouldReceive('browse')->once()->with(['page' => 2])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '2', 'name' => 'Product Two', 'type' => 'stock', 'price' => 20, 'available' => true],
            ],
            'count' => 2,
            'has_next' => false,
            'next_page_url' => null,
            'error_message' => null,
        ]);

        $manager = Mockery::mock(ApiProviderManager::class);
        $manager->shouldReceive('forProvider')->once()->withArgs(function (ApiProvider $resolved) use ($provider) {
            return $resolved->is($provider);
        })->andReturn($catalogService);

        $this->app->instance(ApiProviderManager::class, $manager);

        $this->actingAs($admin)
            ->get(route('admin.providers.catalog.index', $provider))
            ->assertOk()
            ->assertSee('Product One')
            ->assertSee('Product Two')
            ->assertSee('تم عرض الكتالوج كاملاً في صفحة واحدة.');
    }

    public function test_provider_catalog_follows_next_page_url_when_provider_returns_one(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with(['page' => 1])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Product One', 'type' => 'stock', 'price' => 10, 'available' => true],
            ],
            'count' => 2,
            'has_next' => true,
            'next_page_url' => 'https://example.com/catalog?page=2&page_size=500',
            'error_message' => null,
        ]);
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 2,
            'page_url' => 'https://example.com/catalog?page=2&page_size=500',
        ])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '2', 'name' => 'Product Two', 'type' => 'stock', 'price' => 20, 'available' => true],
            ],
            'count' => 2,
            'has_next' => false,
            'next_page_url' => null,
            'error_message' => null,
        ]);

        $manager = Mockery::mock(ApiProviderManager::class);
        $manager->shouldReceive('forProvider')->once()->andReturn($catalogService);

        $this->app->instance(ApiProviderManager::class, $manager);

        $this->actingAs($admin)
            ->get(route('admin.providers.catalog.index', $provider))
            ->assertOk()
            ->assertViewHas('products', function (array $products): bool {
                return count($products) === 2
                    && $products[0]['name'] === 'Product One'
                    && $products[1]['name'] === 'Product Two';
            })
            ->assertViewHas('wasTruncated', false);
    }

    public function test_provider_catalog_stops_when_provider_repeats_the_same_page(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with(['page' => 1])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Product One', 'type' => 'stock', 'price' => 10, 'available' => true],
            ],
            'count' => 40,
            'has_next' => true,
            'next_page_url' => null,
            'error_message' => null,
        ]);
        $catalogService->shouldReceive('browse')->once()->with(['page' => 2])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Product One', 'type' => 'stock', 'price' => 10, 'available' => true],
            ],
            'count' => 40,
            'has_next' => true,
            'next_page_url' => null,
            'error_message' => null,
        ]);

        $manager = Mockery::mock(ApiProviderManager::class);
        $manager->shouldReceive('forProvider')->once()->andReturn($catalogService);

        $this->app->instance(ApiProviderManager::class, $manager);

        $this->actingAs($admin)
            ->get(route('admin.providers.catalog.index', $provider))
            ->assertOk()
            ->assertViewHas('products', fn (array $products): bool => count($products) === 1)
            ->assertViewHas('wasTruncated', true);
    }

    public function test_catalog_service_does_not_append_page_query_when_following_next_page_url(): void
    {
        $provider = $this->makeProvider();

        $client = Mockery::mock(ApiProviderClient::class, [$provider])->makePartial();
        $client->shouldReceive('get')
            ->once()
            ->with('https://example.com/catalog?page=2&page_size=20', [])
            ->andReturn([
                'ok' => true,
                'data' => [
                    'results' => [],
                    'count' => 0,
                    'next' => null,
                ],
            ]);
        $client->shouldReceive('resolvePath')->passthru();

        $service = new ApiProviderCatalogService($provider, $client);

        $result = $service->browse([
            'page' => 2,
            'page_url' => 'https://example.com/catalog?page=2&page_size=20',
        ]);

        $this->assertTrue($result['ok']);
    }

    public function test_catalog_service_falls_back_to_default_param_names_when_provider_values_are_blank(): void
    {
        $provider = $this->makeProvider();
        $provider->forceFill([
            'catalog_page_param' => '',
            'catalog_page_size_param' => '',
            'catalog_page_size' => 20,
        ]);

        $client = Mockery::mock(ApiProviderClient::class, [$provider])->makePartial();
        $client->shouldReceive('get')
            ->once()
            ->with('/catalog', [
                'page' => '3',
                'page_size' => '20',
            ])
            ->andReturn([
                'ok' => true,
                'data' => [
                    'results' => [],
                    'count' => 0,
                    'next' => null,
                ],
            ]);
        $client->shouldReceive('resolvePath')->passthru();

        $service = new ApiProviderCatalogService($provider, $client);

        $result = $service->browse(['page' => 3]);

        $this->assertTrue($result['ok']);
    }

    private function makeProvider(): ApiProvider
    {
        return ApiProvider::create([
            'name' => 'DailyCard',
            'slug' => 'dailycard',
            'is_active' => true,
            'auth_type' => ApiProvider::AUTH_API_KEY_HEADER,
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'base_url' => 'https://example.com',
            'catalog_endpoint' => '/catalog',
            'catalog_method' => 'GET',
            'catalog_response_path' => 'results',
            'catalog_pagination_type' => ApiProvider::PAGINATION_PAGE_NUMBER,
            'catalog_page_param' => 'page',
            'catalog_page_size_param' => 'page_size',
            'catalog_page_size' => 500,
            'field_map_name' => 'name',
            'field_map_price' => 'price',
            'field_map_external_id' => 'id',
        ]);
    }
}
