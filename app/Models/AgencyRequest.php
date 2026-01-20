<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyRequest extends Model
{
    protected $fillable = [
        'contact_number',
        'full_name',
        'region',
        'starting_amount',
    ];

    protected $casts = [
        'starting_amount' => 'decimal:2',
    ];
}
