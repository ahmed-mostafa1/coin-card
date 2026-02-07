<?php

/**
 * VIP Discount Diagnostic Script
 * Run: php artisan tinker < vip_diagnostic.php
 */

echo "=== VIP DISCOUNT DIAGNOSTIC ===\n\n";

// Check VIP Tiers
echo "1. Checking VIP Tiers:\n";
echo "----------------------\n";
$tiers = \App\Models\VipTier::orderBy('rank')->get(['id', 'rank', 'discount_percentage', 'deposits_required', 'title_ar', 'title_en']);
if ($tiers->isEmpty()) {
    echo "❌ NO VIP TIERS FOUND!\n";
    echo "   Please create VIP tiers in the admin panel.\n\n";
} else {
    foreach ($tiers as $tier) {
        echo "✅ Tier {$tier->rank}: {$tier->title_en} ({$tier->title_ar})\n";
        echo "   Discount: {$tier->discount_percentage}%\n";
        echo "   Required Deposits: \${$tier->deposits_required}\n\n";
    }
}

// Check User VIP Statuses
echo "2. Checking User VIP Statuses:\n";
echo "------------------------------\n";
$userStatuses = \App\Models\UserVipStatus::with(['user', 'vipTier'])->get();
if ($userStatuses->isEmpty()) {
    echo "⚠️  NO USERS HAVE VIP STATUS YET\n";
    echo "   Users will get VIP status after making deposits.\n\n";
} else {
    foreach ($userStatuses as $status) {
        echo "User: {$status->user->name} (ID: {$status->user_id})\n";
        echo "  VIP Tier: {$status->vipTier->title_en} (Rank {$status->vipTier->rank})\n";
        echo "  Discount: {$status->vipTier->discount_percentage}%\n";
        echo "  Lifetime Spent: \${$status->lifetime_spent}\n\n";
    }
}

// Check Recent Orders with Discounts
echo "3. Checking Recent Orders with VIP Discounts:\n";
echo "---------------------------------------------\n";
$ordersWithDiscount = \App\Models\Order::where('discount_percentage', '>', 0)
    ->with(['user', 'service'])
    ->latest()
    ->limit(5)
    ->get();

if ($ordersWithDiscount->isEmpty()) {
    echo "⚠️  NO ORDERS WITH VIP DISCOUNTS YET\n\n";
} else {
    foreach ($ordersWithDiscount as $order) {
        echo "Order #{$order->id} - {$order->service->name}\n";
        echo "  User: {$order->user->name}\n";
        echo "  Original Price: \${$order->original_price}\n";
        echo "  Discount: {$order->discount_percentage}%\n";
        echo "  Final Price: \${$order->price_at_purchase}\n";
        echo "  Saved: \${$order->discount_amount}\n\n";
    }
}

// Test VIP Discount Calculation
echo "4. Testing VIP Discount Logic:\n";
echo "------------------------------\n";
$testUser = \App\Models\User::with('vipStatus.vipTier')->first();
if ($testUser) {
    echo "Test User: {$testUser->name}\n";
    if ($testUser->vipStatus && $testUser->vipStatus->vipTier) {
        echo "✅ VIP Status: {$testUser->vipStatus->vipTier->title_en}\n";
        echo "✅ Discount: {$testUser->vipStatus->vipTier->discount_percentage}%\n";
        
        // Test calculation
        $testPrice = 100;
        $discount = $testUser->vipStatus->vipTier->discount_percentage;
        $finalPrice = $testPrice * (1 - $discount / 100);
        echo "\n   Test Calculation:\n";
        echo "   Original: \${$testPrice}\n";
        echo "   Discount: {$discount}%\n";
        echo "   Final: \${$finalPrice}\n";
        echo "   Saved: \$" . ($testPrice - $finalPrice) . "\n";
    } else {
        echo "⚠️  User has no VIP status\n";
    }
} else {
    echo "❌ No users found in database\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "\nIf VIP tiers exist but users don't have VIP status:\n";
echo "- Users need to make deposits to qualify for VIP tiers\n";
echo "- VIP status is calculated based on lifetime_spent\n";
echo "- Run VipService to update user VIP statuses\n";
