<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VerificationRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_NEEDS_CHANGES = 'needs_changes';

    protected $fillable = [
        'user_id',
        'status',
        'payload',
        'review_note',
        'assigned_discount_percentage',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'assigned_discount_percentage' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(VerificationFile::class);
    }
}
