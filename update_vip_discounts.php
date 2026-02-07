// Update VIP tier discount percentages
echo "Updating VIP tier discount percentages...\n\n";

$updates = [
    1 => 2,   // Rank 1 = 2%
    2 => 4,   // Rank 2 = 4%
    3 => 6,   // Rank 3 = 6%
    4 => 8,   // Rank 4 = 8%
    5 => 10,  // Rank 5 = 10%
];

foreach ($updates as $rank => $discount) {
    $tier = \App\Models\VipTier::where('rank', $rank)->first();
    if ($tier) {
        $tier->discount_percentage = $discount;
        $tier->save();
        echo "✅ Rank {$rank} ({$tier->title_en}): {$discount}%\n";
    } else {
        echo "❌ Rank {$rank}: Tier not found\n";
    }
}

echo "\n=== Updated VIP Tiers ===\n";
$tiers = \App\Models\VipTier::orderBy('rank')->get(['rank', 'title_en', 'discount_percentage']);
foreach ($tiers as $tier) {
    echo "Rank {$tier->rank}: {$tier->title_en} - {$tier->discount_percentage}%\n";
}

echo "\nDone! VIP discounts are now configured.\n";
