<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VipTier extends Model
{
    protected $fillable = [
        'name',
        'rank',
        'threshold_amount',
        'badge_image_path',
        'is_active',
    ];

    protected $casts = [
        'threshold_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function userStatuses(): HasMany
    {
        return $this->hasMany(UserVipStatus::class);
    }
}
