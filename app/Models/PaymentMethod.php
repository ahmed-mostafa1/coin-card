<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'instructions',
        'account_number',
        'icon_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function depositRequests(): HasMany
    {
        return $this->hasMany(DepositRequest::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(PaymentMethodField::class);
    }
}
