<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VipTier extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'rank',
        'deposits_required',
        'discount_percentage',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'deposits_required' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the localized title based on current locale
     */
    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->title_en) {
            return $this->title_en;
        }
        return $this->title_ar ?? '';
    }

    public function userStatuses(): HasMany
    {
        return $this->hasMany(UserVipStatus::class);
    }
}
