<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'ticker_text',
                'value' => 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •',
            ],
            [
                'key' => 'ticker_text_en',
                'value' => 'Note for shop owners: Please contact administration for wholesale prices •',
            ],
            [
                'key' => 'logo_type',
                'value' => 'text',
            ],
            [
                'key' => 'logo_text',
                'value' => 'Arab 8BP.in',
            ],
            [
                'key' => 'logo_image',
                'value' => null,
            ],
            [
                'key' => 'store_description',
                'value' => 'متجر عربي متخصص في بيع بطاقات الألعاب والخدمات الرقمية بأسعار تنافسية وجودة عالية. نحن نقدم خدمة سريعة وموثوقة لجميع عملائنا. للاستفسارات أو الدعم، يرجى',
            ],
            [
                'key' => 'whatsapp_link',
                'value' => 'https://wa.me/963991195136',
            ],
            [
                'key' => 'instagram_link',
                'value' => '#',
            ],
            [
                'key' => 'telegram_link',
                'value' => '#',
            ],
            [
                'key' => 'facebook_link',
                'value' => '#',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
