<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVariant extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'name_en',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
