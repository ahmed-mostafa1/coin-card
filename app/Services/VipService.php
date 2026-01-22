<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\UserVipStatus;
use App\Models\VipTier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class VipService
{
    public function calculateLifetimeSpent(User $user): string
    {
        $spent = Order::query()
            ->where('user_id', $user->id)
            ->where(function (Builder $query): void {
                $query->whereNotNull('settled_at')
                    ->orWhere('status', Order::STATUS_DONE);
            })
            ->sum(DB::raw('COALESCE(price_at_purchase, amount_held)'));

        if ($spent === null || $spent === '') {
            return '0.00';
        }

        return number_format((float) $spent, 2, '.', '');
    }

    public function determineTier(float $spent): ?VipTier
    {
        return VipTier::query()
            ->where('is_active', true)
            ->where('threshold_amount', '<=', $spent)
            ->orderByDesc('threshold_amount')
            ->orderByDesc('rank')
            ->first();
    }

    public function getNextTier(?VipTier $current): ?VipTier
    {
        if ($current === null) {
            return VipTier::query()
                ->where('is_active', true)
                ->orderBy('rank')
                ->first();
        }

        return VipTier::query()
            ->where('is_active', true)
            ->where('rank', $current->rank + 1)
            ->first();
    }

    public function updateUserVipStatus(User $user): UserVipStatus
    {
        $spent = $this->calculateLifetimeSpent($user);
        $computedTier = $this->determineTier((float) $spent);

        $existing = UserVipStatus::query()
            ->with('vipTier')
            ->where('user_id', $user->id)
            ->first();

        $finalTier = $computedTier;
        if ($existing?->vipTier && $finalTier === null) {
            $finalTier = $existing->vipTier;
        } elseif ($existing?->vipTier && $finalTier) {
            if ($existing->vipTier->rank > $finalTier->rank) {
                $finalTier = $existing->vipTier;
            }
        }

        return UserVipStatus::updateOrCreate(
            ['user_id' => $user->id],
            [
                'vip_tier_id' => $finalTier?->id,
                'lifetime_spent' => $spent,
                'calculated_at' => now(),
            ]
        )->load('vipTier');
    }

    public function getVipSummary(User $user): array
    {
        $status = $this->updateUserVipStatus($user);
        $spent = (float) ($status->lifetime_spent ?? 0);
        $currentTier = $status->vipTier;
        $nextTier = $this->getNextTier($currentTier);

        if ($nextTier === null) {
            return [
                'spent' => $spent,
                'current_tier' => $currentTier,
                'next_tier' => null,
                'remaining_to_next' => 0.0,
                'progress_percent' => 100.0,
            ];
        }

        $nextThreshold = (float) $nextTier->threshold_amount;
        $remaining = max(0.0, $nextThreshold - $spent);
        $progress = $nextThreshold > 0 ? min(100.0, ($spent / $nextThreshold) * 100) : 0.0;

        return [
            'spent' => $spent,
            'current_tier' => $currentTier,
            'next_tier' => $nextTier,
            'remaining_to_next' => $remaining,
            'progress_percent' => $progress,
        ];
    }
}
