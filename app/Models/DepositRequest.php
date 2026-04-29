<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DepositRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'user_amount',
        'approved_amount',
        'status',
        'user_note',
        'payload',
        'admin_note',
        'reviewed_by_user_id',
        'reviewed_at',
        'currency_id',
        'currency_code',
        'currency_symbol',
        'local_amount',
        'exchange_rate_to_usd',
        'commission_type',
        'commission_value',
        'commission_amount',
        'net_usd_amount',
    ];

    protected $casts = [
        'user_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'payload' => 'array',
        'local_amount' => 'decimal:2',
        'exchange_rate_to_usd' => 'decimal:8',
        'commission_value' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'net_usd_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function evidence(): HasOne
    {
        return $this->hasOne(DepositEvidence::class);
    }
}
