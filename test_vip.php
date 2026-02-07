// Test VIP discount for user
$user = \App\Models\User::with('vipStatus.vipTier')->first();
if($user && $user->vipStatus && $user->vipStatus->vipTier) {
    echo 'User: ' . $user->name . PHP_EOL;
    echo 'VIP Tier: ' . $user->vipStatus->vipTier->title_en . PHP_EOL;
    echo 'Discount: ' . $user->vipStatus->vipTier->discount_percentage . '%' . PHP_EOL;
} else {
    echo 'No VIP user found or user has no VIP status' . PHP_EOL;
    echo 'Creating test VIP status...' . PHP_EOL;
    $user = \App\Models\User::first();
    if ($user) {
        $tier = \App\Models\VipTier::where('rank', 2)->first();
        if ($tier) {
            \App\Models\UserVipStatus::updateOrCreate(
                ['user_id' => $user->id],
                ['vip_tier_id' => $tier->id, 'lifetime_spent' => 500, 'calculated_at' => now()]
            );
            echo 'Created VIP status for user: ' . $user->name . PHP_EOL;
        }
    }
}
