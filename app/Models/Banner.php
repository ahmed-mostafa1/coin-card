<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the localized title based on current locale
     */
    public function getLocalizedTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'en' && $this->title_en 
            ? $this->title_en 
            : $this->title;
    }
}
