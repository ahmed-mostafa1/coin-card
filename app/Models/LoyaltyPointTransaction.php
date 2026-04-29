<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointTransaction extends Model
{
    public const TYPE_ORDER_REWARD = 'order_reward';

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'points',
        'amount_spent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'amount_spent' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
