<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'actor_user_id',
        'action',
        'ip_address',
        'country_code',
        'country',
        'city',
        'device_type',
        'operating_system',
        'browser',
        'user_agent',
        'language',
        'session_id',
        'subject_type',
        'subject_id',
        'order_id',
        'deposit_request_id',
        'wallet_transaction_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
