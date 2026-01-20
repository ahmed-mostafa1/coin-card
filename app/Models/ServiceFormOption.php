<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFormOption extends Model
{
    protected $fillable = [
        'field_id',
        'value',
        'label',
        'sort_order',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServiceFormField::class, 'field_id');
    }
}
