<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceVariant;

class ServicePricingService
{
    public function fixed(Service $service, ?ServiceVariant $variant = null, int $quantity = 1, float $discountPercent = 0): array
    {
        if ($service->is_quantity_based) {
            $basePrice = (float) $service->price_per_unit * max(1, $quantity);
            $feeType = $service->service_fee_type ?? Service::FEE_FIXED;
            $feeValue = (float) ($service->service_fee_value ?? 0);
        } elseif ($variant) {
            $basePrice = (float) $variant->price;
            $feeType = $variant->service_fee_type ?? Service::FEE_FIXED;
            $feeValue = (float) ($variant->service_fee_value ?? 0);
        } else {
            $basePrice = (float) $service->price;
            $feeType = $service->service_fee_type ?? Service::FEE_FIXED;
            $feeValue = (float) ($service->service_fee_value ?? 0);
        }

        return $this->buildQuote($basePrice, $feeType, $feeValue, $discountPercent);
    }

    public function discountedInput(Service $service, float $offerAmount): array
    {
        $feeType = $service->service_fee_type ?? Service::FEE_FIXED;
        $feeValue = (float) ($service->service_fee_value ?? 0);
        $quote = $this->buildQuote($offerAmount, $feeType, $feeValue, 0);

        $legacyPercent = (float) ($service->admin_discount_percent ?? 0);
        if ($legacyPercent !== 0.0) {
            $legacyAdjustment = round($offerAmount * ($legacyPercent / 100), 2);
            $quote['user_discount_percentage'] = $legacyPercent;
            $quote['user_discount_amount'] = $legacyAdjustment;
            $quote['payable_total'] = number_format(max(0, (float) $quote['payable_total'] - $legacyAdjustment), 2, '.', '');
        }

        return $quote;
    }

    public function buildQuote(float $basePrice, string $feeType, float $feeValue, float $discountPercent = 0): array
    {
        $basePrice = round(max(0, $basePrice), 2);
        $feeType = $feeType === Service::FEE_PERCENTAGE ? Service::FEE_PERCENTAGE : Service::FEE_FIXED;
        $feeAmount = $feeType === Service::FEE_PERCENTAGE
            ? $basePrice * ($feeValue / 100)
            : $feeValue;
        $feeAmount = round(max(0, $feeAmount), 2);
        $grossTotal = round($basePrice + $feeAmount, 2);
        $discountPercent = max(0, $discountPercent);
        $discountAmount = round($grossTotal * ($discountPercent / 100), 2);
        $payable = round(max(0, $grossTotal - $discountAmount), 2);

        return [
            'base_price' => number_format($basePrice, 2, '.', ''),
            'service_fee_type' => $feeType,
            'service_fee_value' => number_format($feeValue, 4, '.', ''),
            'service_fee_amount' => number_format($feeAmount, 2, '.', ''),
            'gross_total' => number_format($grossTotal, 2, '.', ''),
            'user_discount_percentage' => number_format($discountPercent, 2, '.', ''),
            'user_discount_amount' => number_format($discountAmount, 2, '.', ''),
            'payable_total' => number_format($payable, 2, '.', ''),
        ];
    }
}
