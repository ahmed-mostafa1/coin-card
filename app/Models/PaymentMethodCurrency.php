<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethodCurrency extends Model
{
    public const COMMISSION_PERCENTAGE = 'percentage';
    public const COMMISSION_FIXED = 'fixed';

    protected $fillable = [
        'payment_method_id',
        'currency_id',
        'exchange_rate_override',
        'commission_type',
        'commission_value',
        'min_amount',
        'max_amount',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate_override' => 'decimal:8',
        'commission_value' => 'decimal:4',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_enabled' => 'boolean',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function effectiveExchangeRate(): string
    {
        return (string) ($this->exchange_rate_override ?: $this->currency->exchange_rate_to_usd);
    }
}
