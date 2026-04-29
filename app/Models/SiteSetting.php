<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function homeFeatureDefaults(): array
    {
        return [
            1 => [
                'icon' => 'fa-solid fa-bolt',
                'title_ar' => 'تجربة موثوقة وسريعة',
                'title_en' => 'Reliable and fast experience',
                'description_ar' => 'نراجع كل طلب يدويًا لضمان الدقة، مع إشعارات فورية وتحديثات واضحة لكل عميل.',
                'description_en' => 'We review every order manually to ensure accuracy, with instant notifications and clear updates for every client.',
            ],
            2 => [
                'icon' => 'fa-solid fa-wallet',
                'title_ar' => 'شحن الرصيد',
                'title_en' => 'Top up balance',
                'description_ar' => 'خصم من الرصيد المتاح فقط',
                'description_en' => 'Deduction from available balance only',
            ],
            3 => [
                'icon' => 'fa-solid fa-sliders',
                'title_ar' => 'نماذج ديناميكية حسب الخدمة',
                'title_en' => 'Dynamic forms per service',
                'description_ar' => 'نماذج الطلب تتكيّف حسب كل خدمة لتقليل الأخطاء وتسريع الإدخال.',
                'description_en' => 'Service forms adapt to each product to reduce mistakes and speed up input.',
            ],
            4 => [
                'icon' => 'fa-solid fa-list-check',
                'title_ar' => 'سجل طلبات ودفعات واضح',
                'title_en' => 'Clear order and payment history',
                'description_ar' => 'سجل واضح للطلبات والرصيد والإشعارات حتى تبقى الصورة كاملة أمامك.',
                'description_en' => 'Clear order, wallet, and notification history so nothing feels hidden.',
            ],
        ];
    }

    public static function homeHeroDefaults(): array
    {
        return [
            'title_ar' => 'منصة احترافية للخدمات الرقمية',
            'title_en' => 'Professional platform for digital services',
            'description_ar' => 'اشحن واشتري خدماتك الرقمية من واجهة واضحة، تنفيذ منظم، ومتابعة دقيقة لكل خطوة.',
            'description_en' => 'Top up and buy digital services through a clear storefront, organized execution, and precise tracking at every step.',
        ];
    }

    public static function get(string $key, $default = null)
    {
        try {
            if (! Schema::hasTable((new static)->getTable())) {
                return $default;
            }

            return static::query()->where('key', $key)->value('value') ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
