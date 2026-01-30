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
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
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
