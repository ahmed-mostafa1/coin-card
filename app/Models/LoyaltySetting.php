<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'points_per_usd',
        'level_3_points',
        'level_2_discount_percentage',
        'level_3_discount_percentage',
        'points_step',
        'discount_step_percentage',
        'max_discount_percentage',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'points_per_usd' => 'decimal:4',
        'level_2_discount_percentage' => 'decimal:2',
        'level_3_discount_percentage' => 'decimal:2',
        'discount_step_percentage' => 'decimal:2',
        'max_discount_percentage' => 'decimal:2',
    ];

    public static function current(): self
    {
        return static::query()->first() ?: static::create([]);
    }
}
