<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'instructions',
        'instructions_en',
        'account_number',
        'icon_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
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
     * Get the localized instructions based on current locale
     */
    public function getLocalizedInstructionsAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' && $this->instructions_en 
            ? $this->instructions_en 
            : $this->instructions;
    }

    public function depositRequests(): HasMany
    {
        return $this->hasMany(DepositRequest::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(PaymentMethodField::class);
    }
}
