<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VipTier extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'rank',
        'deposits_required',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'deposits_required' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function userStatuses(): HasMany
    {
        return $this->hasMany(UserVipStatus::class);
    }
}
