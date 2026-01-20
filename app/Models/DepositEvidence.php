<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositEvidence extends Model
{
    protected $fillable = [
        'deposit_request_id',
        'file_path',
        'file_hash',
        'mime',
        'size',
    ];

    public function depositRequest(): BelongsTo
    {
        return $this->belongsTo(DepositRequest::class);
    }
}
