<?php

namespace Database\Seeders;

use App\Models\VipTier;
use Illuminate\Database\Seeder;

class VipTiersSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['title_en' => 'VIP1', 'rank' => 1, 'deposits_required' => 500.00],
            ['title_en' => 'VIP2', 'rank' => 2, 'deposits_required' => 1000.00],
            ['title_en' => 'VIP3', 'rank' => 3, 'deposits_required' => 2000.00],
            ['title_en' => 'VIP4', 'rank' => 4, 'deposits_required' => 3500.00],
            ['title_en' => 'VIP5', 'rank' => 5, 'deposits_required' => 5000.00],
        ];

        foreach ($tiers as $tier) {
            VipTier::updateOrCreate(
                ['rank' => $tier['rank']],
                [
                    'title_en' => $tier['title_en'],
                    'deposits_required' => $tier['deposits_required'],
                    'is_active' => true,
                ]
            );
        }
    }
}
