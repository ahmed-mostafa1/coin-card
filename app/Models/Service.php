<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'category_id',
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
        'external_product_id',
        'external_type',
        'requires_customer_id',
        'requires_amount',
        'min_quantity',
        'max_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:12',
        'price_per_unit' => 'decimal:12',
        'is_active' => 'boolean',
        'is_offer_active' => 'boolean',
        'is_quantity_based' => 'boolean',
        'requires_customer_id' => 'boolean',
        'requires_amount' => 'boolean',
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
}
