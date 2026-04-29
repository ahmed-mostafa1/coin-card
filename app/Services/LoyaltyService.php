<?php

namespace App\Services;

use App\Models\LoyaltyPointTransaction;
use App\Models\LoyaltySetting;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

class LoyaltyService
{
    public function settings(): LoyaltySetting
    {
        return LoyaltySetting::current();
    }

    public function points(User $user): int
    {
        return (int) $user->loyaltyPointTransactions()->sum('points');
    }

    public function awardForOrder(Order $order): ?LoyaltyPointTransaction
    {
        $settings = $this->settings();
        if (! $settings->is_enabled || $order->status !== Order::STATUS_DONE) {
            return null;
        }

        $amount = (float) ($order->price_at_purchase ?: $order->amount_held ?: 0);
        if ($amount <= 0) {
            return null;
        }

        $points = (int) floor($amount * (float) $settings->points_per_usd);
        if ($points <= 0) {
            return null;
        }

        try {
            return LoyaltyPointTransaction::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'type' => LoyaltyPointTransaction::TYPE_ORDER_REWARD,
                'points' => $points,
                'amount_spent' => number_format($amount, 2, '.', ''),
                'meta' => ['order_status' => $order->status],
            ]);
        } catch (UniqueConstraintViolationException) {
            return null;
        }
    }

    public function discountPercent(User $user): float
    {
        $summary = $this->summary($user);

        return (float) $summary['discount_percentage'];
    }

    public function summary(User $user): array
    {
        $settings = $this->settings();
        $points = $this->points($user);
        $manualDiscount = (float) ($user->verification_discount_percentage ?? 0);

        if (! $user->is_verified) {
            return [
                'level' => 1,
                'level_label' => app()->getLocale() === 'en' ? 'Level 1 - Unverified' : 'المستوى 1 - غير موثق',
                'points' => $points,
                'discount_percentage' => 0.0,
                'progress_percent' => 0.0,
                'next_requirement' => app()->getLocale() === 'en' ? 'Submit and approve account verification.' : 'قدّم طلب التوثيق واحصل على موافقة الإدارة.',
                'settings' => $settings,
            ];
        }

        if (! $settings->is_enabled || $points < (int) $settings->level_3_points) {
            $target = max(1, (int) $settings->level_3_points);
            return [
                'level' => 2,
                'level_label' => app()->getLocale() === 'en' ? 'Level 2 - Verified' : 'المستوى 2 - موثق',
                'points' => $points,
                'discount_percentage' => $manualDiscount ?: (float) $settings->level_2_discount_percentage,
                'progress_percent' => min(100, ($points / $target) * 100),
                'next_requirement' => app()->getLocale() === 'en'
                    ? 'Reach '.$target.' loyalty points to unlock Level 3.'
                    : 'اجمع '.$target.' نقطة ولاء للوصول إلى المستوى 3.',
                'settings' => $settings,
            ];
        }

        $extraPoints = max(0, $points - (int) $settings->level_3_points);
        $steps = (int) floor($extraPoints / max(1, (int) $settings->points_step));
        $loyaltyDiscount = (float) $settings->level_3_discount_percentage + ($steps * (float) $settings->discount_step_percentage);
        $discount = min((float) $settings->max_discount_percentage, max($manualDiscount, $loyaltyDiscount));
        $nextStepPoints = (int) $settings->level_3_points + (($steps + 1) * max(1, (int) $settings->points_step));

        return [
            'level' => 3,
            'level_label' => app()->getLocale() === 'en' ? 'Level 3 - Loyalty' : 'المستوى 3 - الولاء',
            'points' => $points,
            'discount_percentage' => $discount,
            'progress_percent' => min(100, ($points / max(1, $nextStepPoints)) * 100),
            'next_requirement' => app()->getLocale() === 'en'
                ? 'Reach '.$nextStepPoints.' points for the next gradual discount step.'
                : 'اجمع '.$nextStepPoints.' نقطة للوصول إلى خطوة الخصم التالية.',
            'settings' => $settings,
        ];
    }
}
