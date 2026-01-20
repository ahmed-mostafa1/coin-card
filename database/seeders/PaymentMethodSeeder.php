<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $methods = [
            [
                'name' => 'فودافون كاش',
                'slug' => 'vodafone-cash',
                'instructions' => "حول المبلغ إلى الرقم التالي ثم أرفق إثبات التحويل.\n01000000000",
                'sort_order' => 1,
            ],
            [
                'name' => 'انستا باي',
                'slug' => 'instapay',
                'instructions' => "حوّل المبلغ إلى حساب InstaPay التالي ثم أرفق إثبات التحويل.\ninstapay@example",
                'sort_order' => 2,
            ],
            [
                'name' => 'تحويل بنكي',
                'slug' => 'bank-transfer',
                'instructions' => "حوّل المبلغ إلى الحساب البنكي الموضح ثم أرفق إثبات التحويل.\nIBAN SA0000000000000000000000",
                'sort_order' => 3,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['slug' => $method['slug']],
                array_merge($method, ['is_active' => true])
            );
        }
    }
}
