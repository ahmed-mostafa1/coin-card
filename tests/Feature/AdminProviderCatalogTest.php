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

        $provider = $this->makeDailyCardProvider();

        $this->actingAs($admin)
            ->get('/admin/dailycard')
            ->assertRedirect(route('admin.providers.catalog.index', $provider));
    }

    public function test_provider_catalog_renders_all_pages_on_one_screen_for_any_provider(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeGenericProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 1,
            'page_size' => 5000,
        ])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Product One', 'type' => 'stock', 'price' => 10, 'available' => true],
            ],
            'count' => 2,
            'has_next' => true,
            'next_page_url' => null,
            'error_message' => null,
        ]);
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 2,
            'page_size' => 5000,
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
        $manager->shouldReceive('forProvider')->once()->withArgs(function (ApiProvider $resolved) use ($provider) {
            return $resolved->is($provider);
        })->andReturn($catalogService);

        $this->app->instance(ApiProviderManager::class, $manager);

        $this->actingAs($admin)
            ->get(route('admin.providers.catalog.index', [
                'provider' => $provider,
                'mode' => 'all',
            ]))
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

        $provider = $this->makeGenericProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 1,
            'page_size' => 5000,
        ])->andReturn([
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
            'page_size' => 5000,
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
            ->get(route('admin.providers.catalog.index', [
                'provider' => $provider,
                'mode' => 'all',
            ]))
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

        $provider = $this->makeGenericProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 1,
            'page_size' => 5000,
        ])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Product One', 'type' => 'stock', 'price' => 10, 'available' => true],
            ],
            'count' => 40,
            'has_next' => true,
            'next_page_url' => null,
            'error_message' => null,
        ]);
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 2,
            'page_size' => 5000,
        ])->andReturn([
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
            ->get(route('admin.providers.catalog.index', [
                'provider' => $provider,
                'mode' => 'all',
            ]))
            ->assertOk()
            ->assertViewHas('products', fn (array $products): bool => count($products) === 1)
            ->assertViewHas('wasTruncated', true);
    }

    public function test_provider_catalog_page_mode_passes_search_to_provider(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeGenericProvider();

        $catalogService = Mockery::mock();
        $catalogService->shouldReceive('browse')->once()->with([
            'page' => 1,
            'page_size' => 500,
            'search' => 'bigo',
        ])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '1', 'name' => 'Bigo Live 100 Diamonds', 'type' => 'topup', 'price' => 1.77, 'available' => true],
            ],
            'count' => 1,
            'has_next' => false,
            'next_page_url' => null,
            'error_message' => null,
        ]);

        $manager = Mockery::mock(ApiProviderManager::class);
        $manager->shouldReceive('forProvider')->once()->andReturn($catalogService);

        $this->app->instance(ApiProviderManager::class, $manager);

        $this->actingAs($admin)
            ->get(route('admin.providers.catalog.index', [
                'provider' => $provider,
                'mode' => 'page',
                'search' => 'bigo',
            ]))
            ->assertOk()
            ->assertViewHas('mode', 'page')
            ->assertViewHas('search', 'bigo')
            ->assertSee('Bigo Live 100 Diamonds');
    }

    public function test_catalog_service_does_not_append_page_query_when_following_next_page_url(): void
    {
        $provider = $this->makeGenericProvider();

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
        $provider = $this->makeGenericProvider();
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

    public function test_catalog_service_supports_search_filters_and_page_size_override(): void
    {
        $provider = $this->makeGenericProvider();

        $client = Mockery::mock(ApiProviderClient::class, [$provider])->makePartial();
        $client->shouldReceive('get')
            ->once()
            ->with('/catalog', [
                'page' => '1',
                'page_size' => '5000',
                'search' => 'bigo',
                'category' => '12',
                'product_type' => 'stock',
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

        $result = $service->browse([
            'page' => 1,
            'page_size' => 5000,
            'search' => 'bigo',
            'category' => '12',
            'product_type' => 'stock',
        ]);

        $this->assertTrue($result['ok']);
    }

    public function test_dailycard_catalog_service_uses_page_number_params_even_if_provider_pagination_type_is_none(): void
    {
        $provider = $this->makeDailyCardProvider();
        $provider->forceFill([
            'catalog_pagination_type' => ApiProvider::PAGINATION_NONE,
            'catalog_page_param' => '',
            'catalog_page_size_param' => '',
            'catalog_page_size' => 20,
        ]);

        $client = Mockery::mock(ApiProviderClient::class, [$provider])->makePartial();
        $client->shouldReceive('get')
            ->once()
            ->with('/api-keys/products/', [
                'page' => '2',
                'page_size' => '5000',
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

        $result = $service->browse([
            'page' => 2,
            'page_size' => 5000,
        ]);

        $this->assertTrue($result['ok']);
    }

    public function test_hyphenated_dailycard_slug_is_treated_as_dailycard_provider(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $provider = $this->makeDailyCardProvider('daily-card');

        config([
            'services.dailycard.enabled' => true,
            'services.dailycard.base_url' => 'https://dailycard.shop/UAPI',
            'services.dailycard.api_key' => 'test-key',
            'services.dailycard.secret' => 'test-secret',
        ]);

        $this->mock(\App\Services\DailyCardClient::class, function ($mock): void {
            $mock->shouldReceive('isEnabled')->once()->andReturn(true);
            $mock->shouldReceive('getProducts')->twice()->andReturn(
                [
                    'ok' => true,
                    'http_status' => 200,
                    'data' => [
                        'count' => 22,
                        'next' => 2,
                        'previous' => null,
                        'results' => collect(range(1, 20))->map(fn (int $id) => [
                            'id' => $id,
                            'name' => 'Product '.$id,
                            'price' => '1.00',
                            'available' => true,
                            'product_type' => 'stock',
                        ])->all(),
                    ],
                ],
                [
                    'ok' => true,
                    'http_status' => 200,
                    'data' => [
                        'count' => 22,
                        'next' => null,
                        'previous' => 1,
                        'results' => collect(range(21, 22))->map(fn (int $id) => [
                            'id' => $id,
                            'name' => 'Product '.$id,
                            'price' => '1.00',
                            'available' => true,
                            'product_type' => 'stock',
                        ])->all(),
                    ],
                ],
            );
        });

        $this->actingAs($admin)
            ->get(route('admin.providers.catalog.index', [
                'provider' => $provider,
                'mode' => 'page',
                'page' => 2,
            ]))
            ->assertOk()
            ->assertViewHas('isDailyCard', true)
            ->assertViewHas('searchIsLocal', true)
            ->assertViewHas('currentPage', 2)
            ->assertViewHas('products', function (array $products): bool {
                return count($products) === 2
                    && $products[0]['external_id'] === 21
                    && $products[1]['external_id'] === 22;
            });
    }

    private function makeGenericProvider(): ApiProvider
    {
        return ApiProvider::create([
            'name' => 'Test Provider',
            'slug' => 'test-provider',
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

    private function makeDailyCardProvider(string $slug = 'dailycard'): ApiProvider
    {
        return ApiProvider::create([
            'name' => 'DailyCard',
            'slug' => $slug,
            'is_active' => true,
            'auth_type' => ApiProvider::AUTH_API_KEY_HEADER,
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'base_url' => 'https://dailycard.shop/UAPI',
            'catalog_endpoint' => '/api-keys/products/',
            'catalog_method' => 'GET',
            'catalog_response_path' => 'results',
            'catalog_count_path' => 'count',
            'catalog_next_path' => 'next',
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
