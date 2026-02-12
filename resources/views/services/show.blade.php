@extends('layouts.app')

@section('title', $service->localized_name)
@section('mainWidth', 'w-full max-w-full')

@section('content')
    @php
        $availableBalance = $wallet?->balance ?? 0;
        $basePrice = $service->variants->count() ? $service->variants->min('price') : $service->price;
        $isBaseInsufficient = $availableBalance < $basePrice;
        
        // VIP Discount calculation
        $vipDiscount = 0;
        $currentVipTier = null;
        if (auth()->check()) {
            $userVipStatus = auth()->user()->load('vipStatus.vipTier')->vipStatus;
            if ($userVipStatus && $userVipStatus->vipTier) {
                $currentVipTier = $userVipStatus->vipTier;
                $vipDiscount = $currentVipTier->discount_percentage ?? 0;
            }
        }

        $showLimitedOfferLabel = $service->hasLimitedOfferLabel();
        $showLimitedOfferCountdown = $service->hasLimitedOfferCountdown();
        $limitedOfferEndsAtIso = $service->limited_offer_ends_at?->toIso8601String();
    @endphp

    <div class="store-shell space-y-6">
        <div class="w-full sm:w-4/5 sm:mx-auto">
            <x-store.hero :banners="$sharedBanners" :alt="$service->localized_name" />
        </div>

        <div class="w-full sm:w-4/5 sm:mx-auto">
            <x-store.notice :text="$sharedTickerText" />
        </div>

        <div class="w-full sm:w-4/5 sm:mx-auto flex flex-col gap-4">
            @if ($service->is_offer_active)
            <div class="order-1 w-full">
                <div class="store-card relative overflow-hidden p-4">
                    @php
                        $displayPrice = $service->variants->count()
                            ? $service->variants->min('price')
                            : $service->price;
                    @endphp
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        @if ($service->offer_image_path)
                            <img src="{{ asset('storage/' . $service->offer_image_path) }}" alt="{{ $service->localized_name }}"
                                class="h-48 w-full object-cover">
                        @elseif ($service->image_path)
                             <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->localized_name }}"
                                class="h-48 w-full object-cover">
                        @endif
                    </div>

                    <div class="pt-4 text-center">
                        <p class="text-base font-semibold text-emerald-900">{{ __('messages.service-offer-card') }}</p>
                    </div>
                </div>
            </div>
            @endif
            <div class="order-2 w-full space-y-4">
                @if (session('status'))
                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('balance'))
                    <div class="rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-400">
                        {{ $errors->first('balance') }}
                    </div>
                @endif

                <div class="store-card p-6">
                    <div class="flex flex-col items-center justify-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
                        @if ($service->image_path)
                            <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->localized_name }}"
                                class="h-32 w-32 rounded-xl object-cover">
                        @endif
                        <div class="space-y-1 text-center">
                            <h1 class="text-xl font-bold text-slate-900">{{ $service->localized_name }}</h1>
                            <p class="text-sm text-slate-600">{{ $service->category->localized_name }}</p>
                            @if ($showLimitedOfferLabel || $showLimitedOfferCountdown)
                                <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                                    @if ($showLimitedOfferLabel)
                                        <span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:border-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            {{ $service->limited_offer_label }}
                                        </span>
                                    @endif

                                    @if ($showLimitedOfferCountdown)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            <span>{{ __('messages.offer_time_left') }}</span>
                                            <span
                                                data-limited-offer-countdown
                                                data-end-at="{{ $limitedOfferEndsAtIso }}"
                                                class="font-mono font-bold text-amber-700 dark:text-amber-300"
                                            >
                                                -- : -- : --
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @if ($service->localized_description)
                                <p class="mt-2 text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $service->localized_description }}</p>
                            @endif
                            @auth
                                <p class="text-xs text-slate-500">
                                    {{ __('messages.available_balance_text') }}:
                                    <span class="font-semibold text-emerald-700">{{ number_format($availableBalance, 2) }}
                                        USD</span>
                                </p>
                            @endauth
                        </div>
                    </div>

                    <form method="POST" action="{{ route('services.purchase', $service->slug) }}" class="mt-4 space-y-4">
                        @csrf

                        @if ($service->variants->count())
                            <div class="space-y-2">
                                <p class="text-sm font-semibold text-slate-800">{{ __('messages.choose_package') }}</p>
                                <div class="space-y-2">
                                    @foreach ($service->variants->sortBy('sort_order') as $index => $variant)
                                        @php
                                            $originalPrice = $variant->price;
                                            $discountedPrice = $vipDiscount > 0 ? $originalPrice * (1 - $vipDiscount / 100) : $originalPrice;
                                            // Auto-select first variant if no old value
                                            $isChecked = old('variant_id') ? old('variant_id') == $variant->id : $index === 0;
                                        @endphp
                                        <label
                                            class="flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 transition hover:border-emerald-200 dark:hover:border-emerald-500">
                                            <span class="flex items-center gap-2">
                                                <input type="radio" name="variant_id" value="{{ $variant->id }}"
                                                    data-price="{{ $discountedPrice }}"
                                                    data-original-price="{{ $originalPrice }}"
                                                    data-discount="{{ $vipDiscount }}"
                                                    class="text-emerald-600 focus:ring-emerald-500"
                                                    @checked($isChecked) required>
                                                <span>{{ $variant->localized_name }}</span>
                                            </span>
                                            <span class="flex items-center gap-2">
                                                @if ($vipDiscount > 0)
                                                    <span class="text-xs text-slate-500 line-through">${{ number_format($originalPrice, 2) }}</span>
                                                    <span class="font-semibold text-emerald-700">${{ number_format($discountedPrice, 2) }}</span>
                                                @else
                                                    <span class="font-semibold text-emerald-700">${{ number_format($originalPrice, 2) }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('variant_id')" />
                            </div>
                        @endif

                        @if ($service->is_quantity_based)
                            <div class="space-y-2">
                                <p class="text-sm font-semibold text-slate-800">{{ __('messages.quantity') ?? (app()->getLocale() == 'ar' ? 'الكمية' : 'Quantity') }}</p>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="quantity" id="quantity-input" 
                                        min="{{ $service->min_quantity ?? 1 }}" 
                                        @if($service->max_quantity) max="{{ $service->max_quantity }}" @endif
                                        value="{{ $service->min_quantity ?? 1 }}" 
                                        data-price-per-unit="{{ $service->price_per_unit }}"
                                        class="w-32 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-2 text-sm text-slate-700 dark:text-slate-200"
                                        lang="en" dir="ltr"
                                        required>
                                    <span class="text-sm text-slate-600 dark:text-slate-400">
                                        × $<span class="latin-digits" lang="en" dir="ltr">{{ rtrim(rtrim(number_format($service->price_per_unit, 12, '.', ''), '0'), '.') }}</span> {{ __('messages.per_unit') ?? (app()->getLocale() == 'ar' ? 'للوحدة' : 'per unit') }}
                                    </span>
                                    @if($service->min_quantity > 1 || $service->max_quantity)
                                        <p class="text-xs text-slate-500 mt-1">
                                            @if($service->max_quantity)
                                                {!! __('messages.quantity_limits', ['min' => '<span class="latin-digits" lang="en" dir="ltr">'.e($service->min_quantity ?? 1).'</span>', 'max' => '<span class="latin-digits" lang="en" dir="ltr">'.e($service->max_quantity).'</span>']) !!}
                                            @else
                                                {!! __('messages.quantity_min_limit', ['min' => '<span class="latin-digits" lang="en" dir="ltr">'.e($service->min_quantity ?? 1).'</span>']) !!}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($service->localized_additional_rules)
                            <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-300 mb-2">{{ __('messages.additional_rules') ?? (app()->getLocale() == 'ar' ? 'قواعد إضافية' : 'Additional Rules') }}</p>
                                <p class="text-sm text-amber-800 dark:text-amber-200 whitespace-pre-line">{{ $service->localized_additional_rules }}</p>
                            </div>
                        @endif

                        @if ($vipDiscount > 0)
                            <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3">
                                <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-300">
                                    🎉 {{ __('messages.vip_discount_active') ?? (app()->getLocale() == 'ar' ? 'خصم VIP فعّال' : 'VIP Discount Active') }}: {{ number_format($vipDiscount, 0) }}%
                                </p>
                                <p class="text-xs text-emerald-800 dark:text-emerald-200 mt-1">
                                    {{ __('messages.vip_discount_desc') ?? (app()->getLocale() == 'ar' ? 'تستمتع بخصم تلقائي على جميع الخدمات بفضل مستوى VIP الخاص بك' : 'You enjoy automatic discount on all services thanks to your VIP level') }}
                                </p>
                            </div>
                        @endif

                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-3 text-sm text-slate-700 dark:text-slate-200">
                            <p >{{ __('messages.current_price') }}:
                                @if ($service->variants->count())
                                    @php
                                        // Get the first variant (which is auto-selected)
                                        $firstVariant = $service->variants->sortBy('sort_order')->first();
                                        $originalPrice = $firstVariant->price;
                                        $displayPrice = $vipDiscount > 0 ? $originalPrice * (1 - $vipDiscount / 100) : $originalPrice;
                                    @endphp
                                    @if ($vipDiscount > 0)
                                        <span id="original-price" class="text-xs text-slate-500 line-through">${{ number_format($originalPrice, 2) }}</span>
                                    @endif
                                    <span id="current-price" class="font-semibold text-emerald-700">${{ number_format($displayPrice, 2) }}</span>
                                @elseif ($service->is_quantity_based)
                                    @php
                                        $displayPrice = $vipDiscount > 0 ? $service->price_per_unit * (1 - $vipDiscount / 100) : $service->price_per_unit;
                                    @endphp
                                    @if ($vipDiscount > 0)
                                        <span class="text-xs text-slate-500 line-through">${{ number_format($service->price_per_unit, 2) }}</span>
                                    @endif
                                    <span id="current-price" class="font-semibold text-emerald-700">${{ number_format($displayPrice, 2) }}</span>
                                @else
                                    @php
                                        $displayPrice = $vipDiscount > 0 ? $service->price * (1 - $vipDiscount / 100) : $service->price;
                                    @endphp
                                    @if ($vipDiscount > 0)
                                        <span class="text-xs text-slate-500 line-through">${{ number_format($service->price, 2) }}</span>
                                    @endif
                                    <span id="current-price" class="font-semibold text-emerald-700">${{ number_format($displayPrice, 2) }}</span>
                                @endif
                                <span id="price-currency">USD</span>
                            </p>
                            <input type="hidden" name="selected_price" id="selected-price-input" value="@if ($service->variants->count()){{ $displayPrice }}@elseif ($service->is_quantity_based){{ $displayPrice }}@else{{ $vipDiscount > 0 ? $service->price * (1 - $vipDiscount / 100) : $service->price }}@endif">
                            <input type="hidden" name="vip_discount" value="{{ $vipDiscount }}">
                            <p id="insufficient-message"
                                class="mt-2 text-xs text-rose-600 {{ $isBaseInsufficient ? '' : 'hidden' }}">
                                {{ __('messages.insufficient_balance_msg') }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ __('messages.held_amount_notice') }}</p>
                        </div>

                        @forelse ($service->formFields->sortBy('sort_order') as $field)
                            <div class="space-y-1">
                                <label for="field_{{ $field->name_key }}"
                                    class="block text-sm font-semibold text-slate-800">{{ $field->localized_label }}</label>
                                @if ($field->type === 'text')
                                    <input id="field_{{ $field->name_key }}"
                                        name="fields[{{ $field->name_key }}]"
                                        type="text"
                                        value="{{ old('fields.' . $field->name_key) }}"
                                        placeholder="{{ $field->localized_placeholder }}"
                                        class="store-input"
                                        {{ $field->is_required ? 'required' : '' }}>
                                @else
                                    <textarea id="field_{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" rows="3"
                                        placeholder="{{ $field->localized_placeholder }}" class="store-input"
                                        {{ $field->is_required ? 'required' : '' }}>{{ old('fields.' . $field->name_key) }}</textarea>
                                @endif
                                @if ($field->localized_additional_rules)
                                    <p class="mt-2 text-xs text-slate-500">{{ $field->localized_additional_rules }}</p>
                                @endif
                                <x-input-error :messages="$errors->get('fields.' . $field->name_key)" />
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('messages.no_required_fields') }}</p>
                        @endforelse

                        @auth
                            <button id="purchase-button" type="submit"
                                class="w-full rounded-lg bg-[#f2a900] px-5 py-3 text-sm font-semibold text-slate-900 transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60">
                                {{ __('messages.buy_now') }}
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:brightness-105">
                                {{ __('messages.login_to_purchase') }}
                            </a>
                        @endauth
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countdownElement = document.querySelector('[data-limited-offer-countdown]');
            if (!countdownElement) {
                return;
            }

            const endAtRaw = countdownElement.dataset.endAt;
            const endAt = endAtRaw ? new Date(endAtRaw).getTime() : Number.NaN;
            if (Number.isNaN(endAt)) {
                countdownElement.textContent = '-- : -- : --';
                return;
            }

            const purchaseButton = document.getElementById('purchase-button');
            const format = (value) => String(value).padStart(2, '0');

            const render = () => {
                const remainingMs = endAt - Date.now();

                if (remainingMs <= 0) {
                    countdownElement.textContent = '00 : 00 : 00';
                    countdownElement.classList.remove('text-amber-700', 'dark:text-amber-300');
                    countdownElement.classList.add('text-rose-700', 'dark:text-rose-300');

                    if (purchaseButton) {
                        purchaseButton.setAttribute('disabled', 'disabled');
                    }

                    return false;
                }

                const totalMinutes = Math.floor(remainingMs / 60000);
                const days = Math.floor(totalMinutes / (24 * 60));
                const hours = Math.floor((totalMinutes % (24 * 60)) / 60);
                const minutes = totalMinutes % 60;

                countdownElement.textContent = `${days} : ${format(hours)} : ${format(minutes)}`;
                return true;
            };

            const shouldContinue = render();
            if (!shouldContinue) {
                return;
            }

            const intervalId = setInterval(() => {
                if (!render()) {
                    clearInterval(intervalId);
                }
            }, 1000);
        });
    </script>

    @auth
        @php
            $selectPackageMessage = __("messages.select_package_first") ?? (app()->getLocale() == "ar" ? "اختر باقة أولاً" : "Select a package first");
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const availableBalance = parseFloat({{ $availableBalance }} || 0);
                const priceElement = document.getElementById('current-price');
                const originalPriceElement = document.getElementById('original-price');
                const priceCurrency = document.getElementById('price-currency');
                const priceInput = document.getElementById('selected-price-input');
                const insufficientMessage = document.getElementById('insufficient-message');
                const purchaseButton = document.getElementById('purchase-button');
                const variantInputs = document.querySelectorAll('input[name="variant_id"]');
                const quantityInput = document.getElementById('quantity-input');
                const countdownElement = document.querySelector('[data-limited-offer-countdown]');
                const hasVariants = {{ $service->variants->count() ? 'true' : 'false' }};
                const isQuantityBased = {{ $service->is_quantity_based ? 'true' : 'false' }};
                const vipDiscount = {{ $vipDiscount }};

                const formatPrice = (price) => {
                    // Avoid scientific notation and handle small numbers
                    // If price < 0.01 and not 0, show more decimals
                    if (price > 0 && price < 0.01) {
                         return price.toFixed(12).replace(/\.?0+$/, "");
                    }
                    return price.toFixed(2);
                };

                const getSelectedPrice = () => {
                    // Handle quantity-based services
                    if (isQuantityBased) {
                        if (quantityInput) {
                            const quantity = parseInt(quantityInput.value) || 1;
                            const pricePerUnit = parseFloat(quantityInput.dataset.pricePerUnit);
                            let totalPrice = quantity * pricePerUnit;
                            // Apply VIP discount
                            if (vipDiscount > 0) {
                                totalPrice = totalPrice * (1 - vipDiscount / 100);
                            }
                            return totalPrice;
                        }
                    }
                    
                    // Handle variant-based services (discount already applied in data-price)
                    const checked = document.querySelector('input[name="variant_id"]:checked');
                    if (checked) {
                        if (checked.dataset.price) {
                            return parseFloat(checked.dataset.price);
                        }
                    }
                    
                    // Handle regular services
                    let price = hasVariants ? null : parseFloat({{ $service->price }});
                    if (price !== null) {
                        if (vipDiscount > 0) {
                            price = price * (1 - vipDiscount / 100);
                        }
                    }
                    return price;
                };

                const getOriginalPrice = () => {
                    // For variant-based services
                    const checked = document.querySelector('input[name="variant_id"]:checked');
                    if (checked) {
                        if (checked.dataset.originalPrice) {
                            return parseFloat(checked.dataset.originalPrice);
                        }
                    }
                    
                    // For quantity-based services
                    if (isQuantityBased) {
                        if (quantityInput) {
                            const quantity = parseInt(quantityInput.value) || 1;
                            const pricePerUnit = parseFloat(quantityInput.dataset.pricePerUnit);
                            return quantity * pricePerUnit;
                        }
                    }
                    
                    return null;
                };

                const updateState = () => {
                    const price = getSelectedPrice();
                    const originalPrice = getOriginalPrice();
                    const countdownExpired = countdownElement
                        ? (countdownElement.dataset.endAt ? new Date(countdownElement.dataset.endAt).getTime() <= Date.now() : false)
                        : false;
                    
                    if (priceElement) {
                        if (price !== null) {
                            if (!Number.isNaN(price)) {
                                priceElement.textContent = '$' + formatPrice(price);
                                if (priceCurrency) priceCurrency.classList.remove('hidden');
                                if (priceInput) priceInput.value = price.toFixed(12); // Send high precision to backend
                                
                                // Update original price if VIP discount is active
                                if (vipDiscount > 0) {
                                    if (originalPriceElement) {
                                        if (originalPrice !== null) {
                                            originalPriceElement.textContent = '$' + formatPrice(originalPrice);
                                            originalPriceElement.classList.remove('hidden');
                                        }
                                    }
                                } else if (originalPriceElement) {
                                    originalPriceElement.classList.add('hidden');
                                }
                            }
                        } else if (hasVariants) {
                            priceElement.textContent = {!! json_encode($selectPackageMessage) !!};
                            if (priceCurrency) priceCurrency.classList.add('hidden');
                            if (priceInput) priceInput.value = '';
                            if (originalPriceElement) originalPriceElement.classList.add('hidden');
                        }
                    }

                    if (!purchaseButton) {
                        return;
                    }

                    if (countdownExpired || price === null || availableBalance < price) {
                        purchaseButton.setAttribute('disabled', 'disabled');
                        if (insufficientMessage) {
                            if (!countdownExpired && price !== null) {
                                insufficientMessage.classList.remove('hidden');
                            } else if (countdownExpired) {
                                insufficientMessage.classList.add('hidden');
                            }
                        }
                    } else {
                        purchaseButton.removeAttribute('disabled');
                        if (insufficientMessage) {
                            insufficientMessage.classList.add('hidden');
                        }
                    }
                };

                variantInputs.forEach((input) => {
                    input.addEventListener('change', updateState);
                });

                if (quantityInput) {
                    quantityInput.addEventListener('input', updateState);
                }

                updateState();
            });
        </script>
    @endauth
@endsection
