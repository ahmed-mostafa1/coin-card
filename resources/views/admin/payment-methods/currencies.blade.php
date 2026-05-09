@php
    $existingCurrencyConfigs = isset($paymentMethod)
        ? $paymentMethod->currencyConfigs->keyBy('currency_id')
        : collect();
@endphp

<div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-400">عملات طريقة الدفع</h2>
            <p class="mt-1 text-sm text-slate-900 dark:text-slate-400">فعّل العملات المدعومة واضبط العمولة والحدود لكل عملة.</p>
        </div>
        <a href="{{ route('admin.currencies.index') }}" class="text-sm font-semibold text-emerald-900 dark:text-emerald-400">إدارة العملات</a>
    </div>

    <div class="mt-4 space-y-4">
        @foreach ($currencies as $currency)
            @php
                $config = $existingCurrencyConfigs->get($currency->id);
                $oldBase = 'currencies.'.$currency->id.'.';
                $enabled = old($oldBase.'enabled', $config !== null);
                $isEnabled = old($oldBase.'is_enabled', $config?->is_enabled ?? true);
            @endphp
            <div class="rounded-2xl border border-slate-50 p-4 dark:border-slate-700">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-900 dark:text-slate-50">
                        <input type="checkbox" name="currencies[{{ $currency->id }}][enabled]" value="1" class="rounded border-slate-50 text-emerald-600" @checked($enabled)>
                        {{ $currency->name }} ({{ $currency->code }})
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-9000 dark:text-slate-50">
                        <input type="checkbox" name="currencies[{{ $currency->id }}][is_enabled]" value="1" class="rounded border-slate-50 text-emerald-600" @checked($isEnabled)>
                        مفعلة للمستخدم
                    </label>
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-3 xl:grid-cols-6">
                    <div>
                        <x-input-label value="سعر مخصص" />
                        <x-text-input name="currencies[{{ $currency->id }}][exchange_rate_override]" type="number" step="0.00000001" min="0.00000001" dir="ltr" :value="old($oldBase.'exchange_rate_override', $config?->exchange_rate_override)" placeholder="{{ $currency->exchange_rate_to_usd }}" />
                    </div>
                    <div>
                        <x-input-label value="نوع العمولة" />
                        <x-select name="currencies[{{ $currency->id }}][commission_type]">
                            <option value="percentage" @selected(old($oldBase.'commission_type', $config?->commission_type ?? 'percentage') === 'percentage')>نسبة %</option>
                            <option value="fixed" @selected(old($oldBase.'commission_type', $config?->commission_type) === 'fixed')>ثابت</option>
                        </x-select>
                    </div>
                    <div>
                        <x-input-label value="قيمة العمولة" />
                        <x-text-input name="currencies[{{ $currency->id }}][commission_value]" type="number" step="0.0001" min="0" dir="ltr" :value="old($oldBase.'commission_value', $config?->commission_value ?? 0)" />
                    </div>
                    <div>
                        <x-input-label value="الحد الأدنى" />
                        <x-text-input name="currencies[{{ $currency->id }}][min_amount]" type="number" step="0.01" min="0" dir="ltr" :value="old($oldBase.'min_amount', $config?->min_amount)" />
                    </div>
                    <div>
                        <x-input-label value="الحد الأقصى" />
                        <x-text-input name="currencies[{{ $currency->id }}][max_amount]" type="number" step="0.01" min="0" dir="ltr" :value="old($oldBase.'max_amount', $config?->max_amount)" />
                    </div>
                    <div>
                        <x-input-label value="الترتيب" />
                        <x-text-input name="currencies[{{ $currency->id }}][sort_order]" type="number" min="0" :value="old($oldBase.'sort_order', $config?->sort_order ?? 0)" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
