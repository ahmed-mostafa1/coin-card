<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceFormField extends Model
{
    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';

    protected $fillable = [
        'service_id',
        'type',
        'label',
        'label_en',
        'name_key',
        'is_required',
        'placeholder',
        'placeholder_en',
        'sort_order',
        'validation_rules',
        'additional_rules_en',
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
        return $locale === 'en' && $this->label_en 
            ? $this->label_en 
            : $this->label;
    }

    /**
     * Get the localized placeholder based on current locale
     */
    public function getLocalizedPlaceholderAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'en' && $this->placeholder_en 
            ? $this->placeholder_en 
            : $this->placeholder;
    }

    /**
     * Get the localized additional rules based on current locale
     */
    public function getLocalizedAdditionalRulesAttribute(): ?string
    {
        return app()->getLocale() === 'en' ? $this->additional_rules_en : null;
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ServiceFormOption::class, 'field_id');
    }
}
