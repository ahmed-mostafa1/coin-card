<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceFormField extends Model
{
    public const TYPE_TEXT = 'text';
    public const TYPE_SELECT = 'select';

    protected $fillable = [
        'service_id',
        'type',
        'label',
        'name_key',
        'is_required',
        'placeholder',
        'sort_order',
        'validation_rules',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ServiceFormOption::class, 'field_id');
    }
}
