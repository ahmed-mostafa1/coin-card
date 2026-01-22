<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVipStatus extends Model
{
    protected $fillable = [
        'user_id',
        'vip_tier_id',
        'lifetime_spent',
        'calculated_at',
    ];

    protected $casts = [
        'lifetime_spent' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vipTier(): BelongsTo
    {
        return $this->belongsTo(VipTier::class);
    }
}
