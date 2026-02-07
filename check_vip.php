echo "=== VIP DISCOUNT DEBUG ===\n";

// Check if user is logged in
if (!auth()->check()) {
    echo "❌ NOT LOGGED IN - Please login first\n";
    exit;
}

$user = auth()->user();
echo "✅ Logged in as: {$user->name} (ID: {$user->id})\n\n";

// Load VIP status
$user->load('vipStatus.vipTier');

echo "--- VIP Status Check ---\n";
if (!$user->vipStatus) {
    echo "❌ NO VIP STATUS - User has no VIP status assigned\n";
    echo "\nTo fix: Visit http://localhost/coin-card/public/debug/vip-discount\n";
    echo "        and click 'Assign Test VIP Status'\n\n";
    
    // Check if VIP tiers exist
    $tiers = \App\Models\VipTier::count();
    echo "VIP Tiers in database: {$tiers}\n";
    if ($tiers == 0) {
        echo "❌ NO VIP TIERS - Create VIP tiers in admin panel first\n";
    }
    exit;
}

echo "✅ VIP Status exists\n";
echo "   VIP Tier ID: {$user->vipStatus->vip_tier_id}\n";
echo "   Lifetime Spent: \${$user->vipStatus->lifetime_spent}\n\n";

echo "--- VIP Tier Check ---\n";
if (!$user->vipStatus->vipTier) {
    echo "❌ NO VIP TIER - VIP status exists but tier not found\n";
    echo "   This means vip_tier_id points to non-existent tier\n";
    exit;
}

$tier = $user->vipStatus->vipTier;
echo "✅ VIP Tier loaded\n";
echo "   Tier: {$tier->title_en} ({$tier->title_ar})\n";
echo "   Rank: {$tier->rank}\n";
echo "   Discount: {$tier->discount_percentage}%\n\n";

if ($tier->discount_percentage == 0) {
    echo "⚠️  WARNING: Discount is 0%\n";
    echo "   Update VIP tier to have discount > 0\n\n";
} else {
    echo "✅ DISCOUNT CONFIGURED: {$tier->discount_percentage}%\n\n";
    
    // Test calculation
    $testPrice = 100;
    $discounted = $testPrice * (1 - $tier->discount_percentage / 100);
    echo "--- Test Calculation ---\n";
    echo "Original: \${$testPrice}\n";
    echo "Discount: {$tier->discount_percentage}%\n";
    echo "Final: \${$discounted}\n";
    echo "Saved: \$" . ($testPrice - $discounted) . "\n\n";
}

echo "=== RESULT ===\n";
if ($user->vipStatus && $user->vipStatus->vipTier && $user->vipStatus->vipTier->discount_percentage > 0) {
    echo "✅ VIP DISCOUNT SHOULD BE SHOWING\n";
    echo "   If not showing on service page, clear browser cache\n";
    echo "   and refresh the page (Ctrl+Shift+R)\n";
} else {
    echo "❌ VIP DISCOUNT WILL NOT SHOW\n";
    echo "   Fix the issues above first\n";
}
