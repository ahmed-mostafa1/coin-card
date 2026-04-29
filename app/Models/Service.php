<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Service extends Model
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_DAILYCARD = 'dailycard';
    public const SYNC_RULE_AUTO = 'auto';
    public const SYNC_RULE_MANUAL = 'manual';
    public const PRICING_MODE_FIXED = 'fixed';
    public const PRICING_MODE_DISCOUNTED_INPUT = 'discounted_input';
    public const FEE_FIXED = 'fixed';
    public const FEE_PERCENTAGE = 'percentage';
    public const TOPUP_AUTOMATIC = 'automatic';
    public const TOPUP_MANUAL = 'manual';

    protected $fillable = [
        'provider_id',
        'category_id',
        'source',
        'name',
        'name_en',
        'slug',
        'image_path',
        'description',
        'description_en',
        'additional_rules',
        'additional_rules_en',
        'price',
        'service_fee_type',
        'service_fee_value',
        'pricing_mode',
        'admin_discount_percent',
        'is_quantity_based',
        'price_per_unit',
        'is_active',
        'sort_order',
        'offer_image_path',
        'is_offer_active',
        'limited_offer_label',
        'limited_offer_label_en',
        'is_limited_offer_label_active',
        'is_limited_offer_countdown_active',
        'limited_offer_ends_at',
        'external_product_id',
        'external_type',
        'requires_customer_id',
        'requires_amount',
        'provider_payload',
        'provider_price',
        'provider_unit_price',
        'provider_is_available',
        'provider_status',
        'provider_status_raw',
        'provider_status_synced_at',
        'provider_status_message',
        'provider_status_sync_error',
        'provider_availability_managed_by_provider',
        'provider_last_synced_at',
        'sync_rule_mode',
        'supports_purchase_password',
        'requires_purchase_password',
        'last_seen_at',
        'min_quantity',
        'max_quantity',
        'seo_title',
        'seo_meta_description',
        'seo_keywords',
        'seo_content',
        'seo_content_en',
        'is_topup_label_active',
        'topup_label_type',
        'order_image_upload_enabled',
        'order_image_required',
        'order_image_help_text',
    ];

    protected $casts = [
        'price' => 'decimal:12',
        'service_fee_value' => 'decimal:4',
        'admin_discount_percent' => 'decimal:2',
        'price_per_unit' => 'decimal:12',
        'is_active' => 'boolean',
        'is_offer_active' => 'boolean',
        'is_limited_offer_label_active' => 'boolean',
        'is_limited_offer_countdown_active' => 'boolean',
        'limited_offer_ends_at' => 'datetime',
        'is_quantity_based' => 'boolean',
        'requires_customer_id' => 'boolean',
        'requires_amount' => 'boolean',
        'provider_payload' => 'array',
        'provider_price' => 'decimal:4',
        'provider_unit_price' => 'decimal:4',
        'provider_is_available' => 'boolean',
        'provider_status_synced_at' => 'datetime',
        'provider_availability_managed_by_provider' => 'boolean',
        'provider_last_synced_at' => 'datetime',
        'supports_purchase_password' => 'boolean',
        'requires_purchase_password' => 'boolean',
        'last_seen_at' => 'datetime',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_topup_label_active' => 'boolean',
        'order_image_upload_enabled' => 'boolean',
        'order_image_required' => 'boolean',
    ];

    /**
     * Get the localized name based on current locale
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' && $this->name_en 
            ? $this->name_en 
            : $this->name;
    }

    /**
     * Get the localized description based on current locale
     */
    public function getLocalizedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'en' && $this->description_en 
            ? $this->description_en 
            : $this->description;
    }

    public function getLocalizedSeoContentAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'en' && filled($this->seo_content_en)
            ? $this->seo_content_en
            : $this->seo_content;
    }

    public function getTopupLabelTextAttribute(): ?string
    {
        if (! $this->is_topup_label_active || ! in_array($this->topup_label_type, [self::TOPUP_AUTOMATIC, self::TOPUP_MANUAL], true)) {
            return null;
        }

        if ($this->topup_label_type === self::TOPUP_AUTOMATIC) {
            return app()->getLocale() === 'en' ? 'Automatic top-up' : 'شحن تلقائي';
        }

        return app()->getLocale() === 'en' ? 'Manual top-up' : 'شحن يدوي';
    }

    /**
     * Get the localized additional rules based on current locale
     */
    public function getLocalizedAdditionalRulesAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'en' && $this->additional_rules_en 
            ? $this->additional_rules_en 
            : $this->additional_rules;
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ApiProvider::class, 'provider_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(ServiceFormField::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ServiceVariant::class);
    }

    public function buttons(): HasMany
    {
        return $this->hasMany(ServiceButton::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function hasLimitedOfferLabel(): bool
    {
        return $this->is_limited_offer_label_active && filled($this->limited_offer_label);
    }

    public function getLocalizedLimitedOfferLabelAttribute(): ?string
    {
        if (! $this->hasLimitedOfferLabel()) {
            return null;
        }

        $locale = app()->getLocale();

        if ($locale === 'en' && filled($this->limited_offer_label_en)) {
            return $this->limited_offer_label_en;
        }

        return $this->limited_offer_label;
    }

    public function hasLimitedOfferCountdown(): bool
    {
        return $this->is_limited_offer_countdown_active && $this->limited_offer_ends_at !== null;
    }

    public function isLimitedOfferExpired(?Carbon $referenceTime = null): bool
    {
        if (! $this->hasLimitedOfferCountdown()) {
            return false;
        }

        $referenceTime ??= now();

        return $this->limited_offer_ends_at->lte($referenceTime);
    }

    public function scopeManual($query)
    {
        return $query->where(function ($q) {
            $q->where('source', self::SOURCE_MANUAL)
                ->orWhereNull('source');
        });
    }

    public function scopeProviderAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('provider_id')
                ->orWhere(function ($providerQuery) {
                    $providerQuery->where(function ($availabilityQuery) {
                        $availabilityQuery->whereNull('provider_is_available')
                            ->orWhere('provider_is_available', true);
                    })->where(function ($statusQuery) {
                        $statusQuery->whereNull('provider_status')
                            ->orWhereNotIn('provider_status', [
                                'unavailable',
                                'disabled',
                                'stopped',
                                'removed',
                                'unknown',
                            ]);
                    });
                });
        });
    }

    public function isProviderAvailable(): bool
    {
        if ($this->provider_id === null) {
            return true;
        }

        if ($this->provider_is_available === false) {
            return false;
        }

        return ! in_array((string) $this->provider_status, [
            'unavailable',
            'disabled',
            'stopped',
            'removed',
            'unknown',
        ], true);
    }

    public function isDiscountedInputPricing(): bool
    {
        return ($this->pricing_mode ?? self::PRICING_MODE_FIXED) === self::PRICING_MODE_DISCOUNTED_INPUT;
    }
}
