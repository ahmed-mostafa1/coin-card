<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyRequest extends Model
{
    protected $fillable = [
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
