<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSentNotification extends Model
{
    public const SCOPE_SINGLE = 'single';
    public const SCOPE_ALL_USERS = 'all_users';

    protected $fillable = [
        'admin_user_id',
        'target_user_id',
        'scope',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'image_path',
        'recipient_count',
        'ip_address',
        'user_agent',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
