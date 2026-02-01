<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'content',
        'content_en',
        'image_path',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected $appends = ['localized_title', 'localized_content'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLocalizedTitleAttribute()
    {
        return app()->getLocale() === 'en' && $this->title_en 
            ? $this->title_en 
            : $this->title;
    }

    public function getLocalizedContentAttribute()
    {
        return app()->getLocale() === 'en' && $this->content_en 
            ? $this->content_en 
            : $this->content;
    }
}
