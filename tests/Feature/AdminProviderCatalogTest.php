<?php

namespace Tests\Feature;

use App\Models\ApiProvider;
use App\Models\User;
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
            'error_message' => null,
        ]);
        $catalogService->shouldReceive('browse')->once()->with(['page' => 2])->andReturn([
            'ok' => true,
            'products' => [
                ['external_id' => '2', 'name' => 'Product Two', 'type' => 'stock', 'price' => 20, 'available' => true],
            ],
            'count' => 2,
            'has_next' => false,
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
