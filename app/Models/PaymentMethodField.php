<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethodField extends Model
{
    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';

    protected $fillable = [
        'payment_method_id',
        'type',
        'label',
        'name_key',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
