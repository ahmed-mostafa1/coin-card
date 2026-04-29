<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate_to_usd',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate_to_usd' => 'decimal:8',
        'is_enabled' => 'boolean',
    ];

    public function paymentMethodCurrencies(): HasMany
    {
        return $this->hasMany(PaymentMethodCurrency::class);
    }
}
