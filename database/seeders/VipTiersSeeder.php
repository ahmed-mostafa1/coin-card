<?php

namespace Database\Seeders;

use App\Models\VipTier;
use Illuminate\Database\Seeder;

class VipTiersSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['name' => 'VIP1', 'rank' => 1, 'threshold_amount' => 500.00],
            ['name' => 'VIP2', 'rank' => 2, 'threshold_amount' => 1000.00],
            ['name' => 'VIP3', 'rank' => 3, 'threshold_amount' => 2000.00],
            ['name' => 'VIP4', 'rank' => 4, 'threshold_amount' => 3500.00],
            ['name' => 'VIP5', 'rank' => 5, 'threshold_amount' => 5000.00],
        ];

        foreach ($tiers as $tier) {
            VipTier::updateOrCreate(
                ['rank' => $tier['rank']],
                [
                    'name' => $tier['name'],
                    'threshold_amount' => $tier['threshold_amount'],
                    'is_active' => true,
                ]
            );
        }
    }
}
