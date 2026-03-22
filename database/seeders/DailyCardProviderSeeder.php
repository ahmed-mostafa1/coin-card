<?php

namespace Database\Seeders;

use App\Models\ApiProvider;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Migrates DailyCard from .env-based config to the api_providers table.
 * Safe to run multiple times (idempotent via firstOrCreate).
 */
class DailyCardProviderSeeder extends Seeder
{
    public function run(): void
    {
        $provider = ApiProvider::firstOrCreate(
            ['slug' => 'dailycard'],
            [
                'name'      => 'DailyCard Shop',
                'is_active' => filled(config('services.dailycard.api_key')),
                'notes'     => 'مزود DailyCard.shop — تم استيراده تلقائياً من متغيرات البيئة.',

                // Auth
                'auth_type'   => ApiProvider::AUTH_API_KEY_HEADER,
                'credentials' => [
                    'key'    => config('services.dailycard.api_key', ''),
                    'secret' => config('services.dailycard.secret', ''),
                ],
                'timeout' => (int) config('services.dailycard.timeout', 25),

                // Catalog
                'base_url'               => rtrim((string) config('services.dailycard.base_url', 'https://dailycard.shop/UAPI'), '/'),
                'catalog_endpoint'       => '/api-keys/products/',
                'catalog_method'         => 'GET',
                'catalog_response_path'  => 'results',
                'catalog_count_path'     => 'count',
                'catalog_next_path'      => 'next',
                'catalog_pagination_type'=> ApiProvider::PAGINATION_PAGE_NUMBER,
                'catalog_page_param'     => 'page',
                'catalog_page_size_param'=> 'page_size',
                'catalog_page_size'      => 500,

                // Field map
                'field_map_name'        => 'name',
                'field_map_price'       => 'price',
                'field_map_external_id' => 'id',
                'field_map_description' => null,
                'field_map_type'        => 'product_type',
                'field_map_available'   => 'available',

                // Order placement
                'order_endpoint'           => '/api-keys/orders/create/',
                'order_method'             => 'POST',
                'order_our_ref_field'      => 'client_order_id',
                'order_product_field'      => 'product',
                'order_account_id_field'   => 'account_id',
                'order_account_id_source'  => 'customer_identifier',
                'order_quantity_field'     => 'quantity',
                'order_quantity_source'    => 'external_amount',
                'order_extra_fields'       => null,
                'order_transaction_id_path'=> 'transaction_id',
                'order_exec_status_path'   => 'execution_status',

                // Order status
                'status_endpoint'      => '/api-keys/orders/status/',
                'status_method'        => 'GET',
                'status_id_param'      => 'client_order_id',
                'status_exec_path'     => 'execution_status',
                'status_success_values'=> ['success', 'completed', 'done'],
                'status_failed_values' => ['failed', 'error', 'cancelled'],
            ]
        );

        // Link existing services with source = 'dailycard' to this provider
        $updated = Service::where('source', 'dailycard')
            ->whereNull('provider_id')
            ->update(['provider_id' => $provider->id]);

        // Link existing categories with source = 'dailycard'
        $updatedCats = Category::where('source', 'dailycard')
            ->whereNull('provider_id')
            ->update(['provider_id' => $provider->id]);

        $this->command->info("DailyCard provider seeded (ID: {$provider->id}).");
        $this->command->info("Linked {$updated} services and {$updatedCats} categories.");
    }
}
