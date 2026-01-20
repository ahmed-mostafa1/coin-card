<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceFormField;
use App\Models\ServiceFormOption;
use Illuminate\Database\Seeder;

class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $gaming = Category::firstOrCreate([
            'slug' => 'gaming',
        ], [
            'name' => 'بطاقات الألعاب',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $streaming = Category::firstOrCreate([
            'slug' => 'streaming',
        ], [
            'name' => 'خدمات البث',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $service1 = Service::firstOrCreate([
            'slug' => 'pubg-uc',
        ], [
            'category_id' => $gaming->id,
            'name' => 'شحن شدات PUBG',
            'price' => 150,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $service2 = Service::firstOrCreate([
            'slug' => 'steam-wallet',
        ], [
            'category_id' => $gaming->id,
            'name' => 'بطاقة ستيم',
            'price' => 200,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $service3 = Service::firstOrCreate([
            'slug' => 'netflix',
        ], [
            'category_id' => $streaming->id,
            'name' => 'اشتراك نتفليكس',
            'price' => 90,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $service4 = Service::firstOrCreate([
            'slug' => 'osn',
        ], [
            'category_id' => $streaming->id,
            'name' => 'اشتراك OSN',
            'price' => 110,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $field = ServiceFormField::firstOrCreate([
            'service_id' => $service1->id,
            'name_key' => 'player_id',
        ], [
            'type' => 'text',
            'label' => 'رقم اللاعب',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $regionField = ServiceFormField::firstOrCreate([
            'service_id' => $service1->id,
            'name_key' => 'region',
        ], [
            'type' => 'select',
            'label' => 'المنطقة',
            'is_required' => true,
            'sort_order' => 2,
        ]);

        ServiceFormOption::firstOrCreate([
            'field_id' => $regionField->id,
            'value' => 'mena',
        ], [
            'label' => 'الشرق الأوسط',
            'sort_order' => 1,
        ]);

        ServiceFormOption::firstOrCreate([
            'field_id' => $regionField->id,
            'value' => 'eu',
        ], [
            'label' => 'أوروبا',
            'sort_order' => 2,
        ]);
    }
}
