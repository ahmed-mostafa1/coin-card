<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiProvider extends Model
{
    // ── Auth type constants ───────────────────────────────────────────────────
    public const AUTH_API_KEY_HEADER    = 'api_key_header';
    public const AUTH_API_KEY_QUERY     = 'api_key_query';
    public const AUTH_BASIC_HEADER      = 'basic_auth_header';
    public const AUTH_BASIC_QUERY       = 'basic_auth_query';
    public const AUTH_BEARER_HEADER     = 'bearer_header';

    public const AUTH_TYPES = [
        self::AUTH_API_KEY_HEADER => 'API Key — Header',
        self::AUTH_API_KEY_QUERY  => 'API Key — Query Parameter',
        self::AUTH_BASIC_HEADER   => 'Basic Auth — Header',
        self::AUTH_BASIC_QUERY    => 'Basic Auth — Query Parameter',
        self::AUTH_BEARER_HEADER  => 'Bearer Token — Header',
    ];

    // ── Pagination type constants ─────────────────────────────────────────────
    public const PAGINATION_NONE        = 'none';
    public const PAGINATION_PAGE_NUMBER = 'page_number';
    public const PAGINATION_OFFSET      = 'offset';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'notes',
        // Auth
        'auth_type',
        'credentials',
        'timeout',
        // Catalog
        'base_url',
        'catalog_endpoint',
        'catalog_method',
        'catalog_response_path',
        'catalog_count_path',
        'catalog_next_path',
        'catalog_pagination_type',
        'catalog_page_param',
        'catalog_page_size_param',
        'catalog_page_size',
        // Field map
        'field_map_name',
        'field_map_price',
        'field_map_external_id',
        'field_map_description',
        'field_map_type',
        'field_map_available',
        // Order placement
        'order_endpoint',
        'order_method',
        'order_our_ref_field',
        'order_product_field',
        'order_account_id_field',
        'order_account_id_source',
        'order_quantity_field',
        'order_quantity_source',
        'order_extra_fields',
        'order_transaction_id_path',
        'order_exec_status_path',
        // Order status
        'status_endpoint',
        'status_method',
        'status_id_param',
        'status_exec_path',
        'status_success_values',
        'status_failed_values',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'credentials'          => 'encrypted:array',
        'order_extra_fields'   => 'array',
        'status_success_values'=> 'array',
        'status_failed_values' => 'array',
        'timeout'              => 'integer',
        'catalog_page_size'    => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'provider_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function hasOrderFulfillment(): bool
    {
        return filled($this->order_endpoint);
    }

    public function hasStatusCheck(): bool
    {
        return filled($this->status_endpoint);
    }

    public function getCredential(string $key): ?string
    {
        return $this->credentials[$key] ?? null;
    }

    public function fullUrl(string $path): string
    {
        return rtrim($this->base_url, '/') . '/' . ltrim($path, '/');
    }
}
