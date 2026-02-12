<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Service extends Model
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_MARKETCARD99 = 'marketcard99';
    public const SYNC_RULE_AUTO = 'auto';
    public const SYNC_RULE_MANUAL = 'manual';

    protected $fillable = [
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
        'provider_last_synced_at',
        'sync_rule_mode',
        'supports_purchase_password',
        'requires_purchase_password',
        'last_seen_at',
        'min_quantity',
        'max_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:12',
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
        'provider_last_synced_at' => 'datetime',
        'supports_purchase_password' => 'boolean',
        'requires_purchase_password' => 'boolean',
        'last_seen_at' => 'datetime',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
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
}
