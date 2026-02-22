<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceButton extends Model
{
    protected $fillable = [
        'service_id',
        'label_ar',
        'label_en',
        'url',
        'bg_color',
        'sort_order',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the localized label based on current locale.
     */
    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'en' && filled($this->label_en)
            ? $this->label_en
            : $this->label_ar;
    }
}
