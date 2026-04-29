<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIpHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'country_code',
        'country',
        'city',
        'first_seen_at',
        'last_seen_at',
        'seen_count',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'seen_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
