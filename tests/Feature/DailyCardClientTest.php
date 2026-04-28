<?php

namespace Tests\Feature;

use App\Models\ApiProvider;
use App\Services\DailyCardClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DailyCardClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_database_dailycard_provider_credentials_before_env_fallback(): void
    {
        config([
            'services.dailycard.enabled' => true,
            'services.dailycard.base_url' => 'https://env.example/UAPI',
            'services.dailycard.api_key' => 'env-key',
            'services.dailycard.secret' => 'env-secret',
            'services.dailycard.timeout' => 25,
        ]);

        ApiProvider::create([
            'name' => 'DailyCard',
            'slug' => 'daily-card',
            'is_active' => true,
            'auth_type' => ApiProvider::AUTH_API_KEY_HEADER,
            'credentials' => ['key' => 'db-key', 'secret' => 'db-secret'],
            'timeout' => 30,
            'base_url' => 'https://db.example/UAPI',
            'catalog_endpoint' => '/api-keys/products/',
            'catalog_method' => 'GET',
            'catalog_pagination_type' => ApiProvider::PAGINATION_PAGE_NUMBER,
            'field_map_name' => 'name',
            'field_map_price' => 'price',
        ]);

        Http::fake(function (Request $request) {
            $this->assertSame('https://db.example/UAPI/api-keys/products/?page=1', (string) $request->url());
            $this->assertSame(['db-key'], $request->header('X-API-Key'));
            $this->assertSame(['db-secret'], $request->header('X-API-Secret'));
            $this->assertSame(['application/json'], $request->header('Content-Type'));

            return Http::response(['results' => []]);
        });

        $result = app(DailyCardClient::class)->getProducts(['page' => 1]);

        $this->assertTrue($result['ok']);
    }

    public function test_it_uses_env_credentials_when_database_provider_is_missing(): void
    {
        config([
            'services.dailycard.enabled' => true,
            'services.dailycard.base_url' => 'https://env.example/UAPI',
            'services.dailycard.api_key' => 'env-key',
            'services.dailycard.secret' => 'env-secret',
            'services.dailycard.timeout' => 25,
        ]);

        Http::fake(function (Request $request) {
            $this->assertSame('https://env.example/UAPI/api-keys/user/info/', (string) $request->url());
            $this->assertSame(['env-key'], $request->header('X-API-Key'));
            $this->assertSame(['env-secret'], $request->header('X-API-Secret'));
            $this->assertSame(['application/json'], $request->header('Content-Type'));

            return Http::response(['user' => 'ok']);
        });

        $result = app(DailyCardClient::class)->getUserInfo();

        $this->assertTrue($result['ok']);
    }
}
