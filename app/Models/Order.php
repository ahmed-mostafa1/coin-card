<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'service_id',
        'variant_id',
        'status',
        'price_at_purchase',
        'original_price',
        'discount_percentage',
        'discount_amount',
        'amount_held',
        'payload',
        'admin_note',
        'handled_by_user_id',
        'handled_at',
        'settled_at',
        'released_at',
    ];

    protected $casts = [
        'price_at_purchase' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount_held' => 'decimal:2',
        'payload' => 'array',
        'handled_at' => 'datetime',
        'settled_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'variant_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class);
    }
}
