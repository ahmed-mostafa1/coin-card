<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationField extends Model
{
    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_NUMBER = 'number';
    public const TYPE_DATE = 'date';
    public const TYPE_SELECT = 'select';
    public const TYPE_FILE = 'file';
    public const TYPE_IMAGE = 'image';
    public const TYPE_CAMERA = 'camera';

    protected $fillable = [
        'label',
        'label_en',
        'name_key',
        'type',
        'options',
        'placeholder',
        'placeholder_en',
        'is_required',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    public function getLocalizedLabelAttribute(): string
    {
        return app()->getLocale() === 'en' && filled($this->label_en) ? $this->label_en : $this->label;
    }

    public function getLocalizedPlaceholderAttribute(): ?string
    {
        return app()->getLocale() === 'en' && filled($this->placeholder_en) ? $this->placeholder_en : $this->placeholder;
    }

    public function isFileType(): bool
    {
        return in_array($this->type, [self::TYPE_FILE, self::TYPE_IMAGE, self::TYPE_CAMERA], true);
    }
}
