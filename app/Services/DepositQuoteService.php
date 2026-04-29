<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\PaymentMethodCurrency;
use Illuminate\Validation\ValidationException;

class DepositQuoteService
{
    public function quote(PaymentMethodCurrency $config, string|float|int $localAmount): array
    {
        $amount = round((float) $localAmount, 2);
        $rate = round((float) $config->effectiveExchangeRate(), 8);

        if ($amount <= 0 || $rate <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'يرجى إدخال مبلغ وسعر صرف صالحين.',
            ]);
        }

        if ($config->min_amount !== null && $amount < (float) $config->min_amount) {
            throw ValidationException::withMessages([
                'amount' => 'المبلغ أقل من الحد الأدنى لهذه العملة.',
            ]);
        }

        if ($config->max_amount !== null && $amount > (float) $config->max_amount) {
            throw ValidationException::withMessages([
                'amount' => 'المبلغ أكبر من الحد الأقصى لهذه العملة.',
            ]);
        }

        $commissionAmount = $config->commission_type === PaymentMethodCurrency::COMMISSION_FIXED
            ? (float) $config->commission_value
            : $amount * ((float) $config->commission_value / 100);

        $commissionAmount = round(min(max($commissionAmount, 0), $amount), 2);
        $netUsd = round(max(0, $amount - $commissionAmount) * $rate, 2);

        return [
            'currency_id' => $config->currency_id,
            'currency_code' => $config->currency->code,
            'currency_symbol' => $config->currency->symbol,
            'local_amount' => number_format($amount, 2, '.', ''),
            'exchange_rate_to_usd' => number_format($rate, 8, '.', ''),
            'commission_type' => $config->commission_type,
            'commission_value' => number_format((float) $config->commission_value, 4, '.', ''),
            'commission_amount' => number_format($commissionAmount, 2, '.', ''),
            'net_usd_amount' => number_format($netUsd, 2, '.', ''),
        ];
    }

    public function enabledConfigFor(PaymentMethod $paymentMethod, int|string|null $currencyId): PaymentMethodCurrency
    {
        $config = $paymentMethod->currencyConfigs()
            ->with('currency')
            ->where('currency_id', $currencyId)
            ->where('is_enabled', true)
            ->whereHas('currency', fn ($query) => $query->where('is_enabled', true))
            ->first();

        if (! $config) {
            $hasAnyConfigs = $paymentMethod->currencyConfigs()->exists();
            if (! $hasAnyConfigs) {
                $usd = \App\Models\Currency::query()->where('code', 'USD')->where('is_enabled', true)->first();
                if ($usd && (int) $currencyId === (int) $usd->id) {
                    return $paymentMethod->currencyConfigs()->create([
                        'currency_id' => $usd->id,
                        'commission_type' => PaymentMethodCurrency::COMMISSION_PERCENTAGE,
                        'commission_value' => 0,
                        'is_enabled' => true,
                        'sort_order' => 0,
                    ])->load('currency');
                }
            }

            throw ValidationException::withMessages([
                'currency_id' => 'العملة المحددة غير متاحة لطريقة الدفع هذه.',
            ]);
        }

        return $config;
    }
}
