<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationFile extends Model
{
    protected $fillable = [
        'verification_request_id',
        'verification_field_id',
        'field_key',
        'file_path',
        'mime',
        'size',
        'file_hash',
    ];

    public function verificationRequest(): BelongsTo
    {
        return $this->belongsTo(VerificationRequest::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(VerificationField::class, 'verification_field_id');
    }
}
