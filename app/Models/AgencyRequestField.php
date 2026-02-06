<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyRequestField extends Model
{
    protected $fillable = [
        'label',
        'label_en',
        'name_key',
        'type',
        'is_required',
        'placeholder',
        'placeholder_en',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the localized label based on current locale
     */
    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->label_en) {
            return $this->label_en;
        }
        return $this->label ?? '';
    }

    /**
     * Get the localized placeholder based on current locale
     */
    public function getLocalizedPlaceholderAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->placeholder_en) {
            return $this->placeholder_en;
        }
        return $this->placeholder;
    }
}
