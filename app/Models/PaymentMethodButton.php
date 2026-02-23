<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethodButton extends Model
{
    protected $fillable = [
        'payment_method_id',
        'label_ar',
        'label_en',
        'url',
        'bg_color',
        'sort_order',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'en' && filled($this->label_en)
            ? $this->label_en
            : $this->label_ar;
    }
}
