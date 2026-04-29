@extends('layouts.app')

@php
    $serviceImage = $service->offer_image_path
        ? asset('storage/'.$service->offer_image_path)
        : ($service->image_path ? asset('storage/'.$service->image_path) : asset('img/placeholder-category.jpg'));
    $serviceMetaDescriptionSource = $service->localized_description ?: (
        app()->getLocale() === 'ar'
            ? 'شراء '.$service->localized_name.' ضمن قسم '.$service->category->localized_name.' مع تنفيذ واضح وتسعير محدث.'
            : 'Buy '.$service->localized_name.' in '.$service->category->localized_name.' with clear fulfillment details and updated pricing.'
    );
    $serviceMetaDescription = \Illuminate\Support\Str::limit(strip_tags($serviceMetaDescriptionSource), 160, '');
    $serviceTitle = $service->localized_name;
    $variantPrices = $service->variants->pluck('price')->filter();
    $offersSchema = $variantPrices->count() > 1
        ? [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'USD',
            'lowPrice' => number_format((float) $variantPrices->min(), 2, '.', ''),
            'highPrice' => number_format((float) $variantPrices->max(), 2, '.', ''),
            'offerCount' => $variantPrices->count(),
            'availability' => $service->isProviderAvailable() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('services.show', $service->slug),
        ]
        : [
            '@type' => 'Offer',
            'priceCurrency' => 'USD',
            'price' => number_format((float) ($service->is_quantity_based ? $service->price_per_unit : ($variantPrices->first() ?? $service->price)), 2, '.', ''),
            'availability' => $service->isProviderAvailable() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('services.show', $service->slug),
        ];
    $productSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $service->localized_name,
        'description' => $serviceMetaDescription,
        'image' => $serviceImage ? [$serviceImage] : null,
        'sku' => $service->slug,
        'category' => $service->category->localized_name,
        'brand' => [
            '@type' => 'Brand',
            'name' => $sharedLogoText ?: config('app.name', 'S7SH.com|شحنك شات'),
        ],
        'offers' => $offersSchema,
    ], fn ($value) => $value !== null && $value !== []);
    $serviceBreadcrumbItems = array_values(array_filter([
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home',
            'item' => route('home'),
        ],
        $service->category->parent ? [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $service->category->parent->localized_name,
            'item' => route('categories.show', $service->category->parent->slug),
        ] : null,
        [
            '@type' => 'ListItem',
            'position' => $service->category->parent ? 3 : 2,
            'name' => $service->category->localized_name,
            'item' => route('categories.show', $service->category->slug),
        ],
        [
            '@type' => 'ListItem',
            'position' => $service->category->parent ? 4 : 3,
            'name' => $service->localized_name,
            'item' => route('services.show', $service->slug),
        ],
    ]));
    $serviceBreadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $serviceBreadcrumbItems,
    ];
    $shareLabel = app()->getLocale() === 'ar' ? 'مشاركة الخدمة' : 'Share service';
    $shareCopiedLabel = app()->getLocale() === 'ar' ? 'تم نسخ رابط الخدمة' : 'Service link copied';
    $shareFailedLabel = app()->getLocale() === 'ar' ? 'تعذر نسخ الرابط' : 'Failed to copy link';
@endphp

@section('title', $service->seo_title ?: $serviceTitle)
@section('meta_description', $service->seo_meta_description ?: $serviceMetaDescription)
@section('meta_keywords', $service->seo_keywords ?? '')
@section('meta_canonical', route('services.show', $service->slug))
@section('meta_type', 'product')
@section('meta_image', $serviceImage)
@section('meta_robots', 'index,follow')
@section('mainWidth', 'w-[85%] mx-auto')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($serviceBreadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <style>
        .offer-countdown {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            align-items: flex-end;
            gap: 0.3rem;
            direction: ltr;
            width: 100%;
        }

        .offer-countdown__unit {
            min-width: 0;
            text-align: center;
            color: #b30010;
        }

        .offer-countdown__value {
            display: block;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: clamp(0.82rem, 1.6vw, 1.12rem);
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.01em;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.85), 0 0 8px rgba(179, 0, 16, 0.15);
        }

        .offer-countdown__label {
            display: block;
            margin-top: 0.18rem;
            padding-top: 0.18rem;
            border-top: 2px solid #d82929;
            font-size: clamp(0.48rem, 1vw, 0.62rem);
            font-weight: 700;
            line-height: 1;
            color: #b30010;
        }

        @media (max-width: 639px) {
            .offer-countdown {
                gap: 0.18rem;
            }

            .offer-countdown__value {
                font-size: clamp(0.64rem, 2.9vw, 0.86rem);
                letter-spacing: 0;
            }

            .offer-countdown__label {
                font-size: clamp(0.42rem, 2vw, 0.54rem);
            }
        }
    </style>

    @php
        $availableBalance = $wallet?->balance ?? 0;
        $isDiscountedInputPricing = $service->isDiscountedInputPricing();
        $serviceDiscountPercent = $isDiscountedInputPricing ? (float) ($service->admin_discount_percent ?? 0) : 0;
        $basePrice = $isDiscountedInputPricing
            ? 0
            : ($service->variants->count() ? $service->variants->min('price') : $service->price);
        $isBaseInsufficient = $availableBalance < $basePrice;
        
        $loyaltySummary = auth()->check() ? app(\App\Services\LoyaltyService::class)->summary(auth()->user()) : null;
        $vipDiscount = (! $isDiscountedInputPricing && $loyaltySummary) ? (float) ($loyaltySummary['discount_percentage'] ?? 0) : 0;

        $showLimitedOfferLabel = $service->hasLimitedOfferLabel();
        $showLimitedOfferCountdown = $service->hasLimitedOfferCountdown();
        $limitedOfferEndsAtIso = $service->limited_offer_ends_at?->toIso8601String();
        $countdownLabels = app()->getLocale() === 'ar'
            ? ['days' => 'أيام', 'hours' => 'ساعات', 'minutes' => 'دقائق', 'seconds' => 'ثواني']
            : ['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes', 'seconds' => 'Seconds'];
    @endphp

    <div class="store-shell space-y-6">
        <div class="w-full">
            <x-store.notice :text="$sharedTickerText" />
        </div>

        <div class="flex w-full flex-col gap-4">
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
                                class="w-full object-fill">
                        @elseif ($service->image_path)
                             <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->localized_name }}"
                                class="w-full object-fill">
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
                            <div class="relative">
                                <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->localized_name }}"
                                    class="h-32 w-32 rounded-xl object-cover">
                                @if ($service->topup_label_text)
                                    <span class="absolute -end-2 -top-2 rounded-full bg-emerald-600 px-2.5 py-1 text-[11px] font-bold text-white shadow">{{ $service->topup_label_text }}</span>
                                @endif
                            </div>
                        @endif
                        <div class="space-y-1 text-center">
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $service->localized_name }}</h1>
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $service->category->localized_name }}</p>
                            <div class="pt-2">
                                <button type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300"
                                        data-share-service
                                        data-share-title="{{ $service->localized_name }}"
                                        data-share-url="{{ route('services.show', $service->slug) }}"
                                        data-share-success="{{ $shareCopiedLabel }}"
                                        data-share-failure="{{ $shareFailedLabel }}">
                                    <i class="fa-solid fa-share-nodes text-xs"></i>
                                    <span>{{ $shareLabel }}</span>
                                </button>
                            </div>
                            @if ($showLimitedOfferLabel || $showLimitedOfferCountdown)
                                <div class="mt-2 flex items-center justify-center gap-2">
                                    @if ($showLimitedOfferLabel)
                                        <span class="inline-flex shrink-0 items-center border border-rose-200 bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700 dark:border-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            {{ $service->localized_limited_offer_label }}
                                        </span>
                                    @endif

                                    @if ($showLimitedOfferCountdown)
                                        <div class="min-w-0 flex-1 border border-slate-200 bg-slate-100 px-2 py-1 dark:border-slate-700 dark:bg-slate-800">
                                            <div class="offer-countdown" data-limited-offer-countdown data-end-at="{{ $limitedOfferEndsAtIso }}">
                                                <div class="offer-countdown__unit">
                                                    <span class="offer-countdown__value" data-countdown-days>---</span>
                                                    <span class="offer-countdown__label">{{ $countdownLabels['days'] }}</span>
                                                </div>
                                                <div class="offer-countdown__unit">
                                                    <span class="offer-countdown__value" data-countdown-hours>--</span>
                                                    <span class="offer-countdown__label">{{ $countdownLabels['hours'] }}</span>
                                                </div>
                                                <div class="offer-countdown__unit">
                                                    <span class="offer-countdown__value" data-countdown-minutes>--</span>
                                                    <span class="offer-countdown__label">{{ $countdownLabels['minutes'] }}</span>
                                                </div>
                                                <div class="offer-countdown__unit">
                                                    <span class="offer-countdown__value" data-countdown-seconds>--</span>
                                                    <span class="offer-countdown__label">{{ $countdownLabels['seconds'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if ($service->localized_description)
                                <p class="mt-2 text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $service->localized_description }}</p>
                            @endif

                            @if ($service->buttons->isNotEmpty())
                                <div class="mt-4 flex flex-wrap justify-center gap-3 w-full">
                                    @foreach ($service->buttons->sortBy('sort_order') as $btn)
                                        <a href="{{ $btn->url }}" target="_blank" rel="noopener noreferrer"
                                            class="flex w-full sm:w-auto items-center justify-center text-center rounded-lg px-6 py-3 min-w-[140px] text-sm font-semibold text-white shadow-sm transition hover:brightness-110 active:scale-95"
                                            data-button-bg="{{ $btn->bg_color }}">
                                            {{ $btn->localized_label }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            @auth
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ __('messages.available_balance_text') }}:
                                    <span class="font-semibold text-emerald-700">{{ number_format($availableBalance, 2) }}
                                        USD</span>
                                </p>
                            @endauth
                        </div>
                    </div>

                    <form id="purchase-form" method="POST" action="{{ route('services.purchase', $service->slug) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf

                        @if (! $isDiscountedInputPricing && $service->variants->count())
                            <div class="space-y-2">
                                <p class="text-sm font-semibold text-slate-800">{{ __('messages.choose_package') }}</p>
                                <div class="space-y-2">
                                    @foreach ($service->variants->sortBy('sort_order') as $index => $variant)
                                        @php
                                            $originalPrice = $variant->price;
                                            $feeAmount = ($variant->service_fee_type ?? 'fixed') === 'percentage'
                                                ? $originalPrice * ((float) $variant->service_fee_value / 100)
                                                : (float) $variant->service_fee_value;
                                            $grossPrice = $originalPrice + $feeAmount;
                                            $discountedPrice = $vipDiscount > 0 ? $grossPrice * (1 - $vipDiscount / 100) : $grossPrice;
                                            // Auto-select first variant if no old value
                                            $isChecked = old('variant_id') ? old('variant_id') == $variant->id : $index === 0;
                                        @endphp
                                        <label
                                            class="flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 transition hover:border-emerald-200 dark:hover:border-emerald-500">
                                            <span class="flex items-center gap-2">
                                                <input type="radio" name="variant_id" value="{{ $variant->id }}"
                                                    data-price="{{ $discountedPrice }}"
                                                    data-original-price="{{ $originalPrice }}"
                                                    data-fee-amount="{{ $feeAmount }}"
                                                    data-gross-price="{{ $grossPrice }}"
                                                    data-variant-name="{{ $variant->localized_name }}"
                                                    data-discount="{{ $vipDiscount }}"
                                                    class="text-emerald-600 focus:ring-emerald-500"
                                                    @checked($isChecked) required>
                                                <span>{{ $variant->localized_name }}</span>
                                            </span>
                                            <span class="flex items-center gap-2">
                                                @if ($vipDiscount > 0)
                                                    <span class="text-xs text-slate-500 line-through">${{ number_format($grossPrice, 2) }}</span>
                                                    <span class="font-semibold text-emerald-700">${{ number_format($discountedPrice, 2) }}</span>
                                                @else
                                                    <span class="font-semibold text-emerald-700">${{ number_format($grossPrice, 2) }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('variant_id')" />
                            </div>
                        @endif

                        @if (! $isDiscountedInputPricing && $service->is_quantity_based)
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

                        @if (! $isDiscountedInputPricing && $vipDiscount > 0)
                            <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3">
                                <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-300">
                                    {{ app()->getLocale() == 'ar' ? 'خصم الحساب فعّال' : 'Account discount active' }}: {{ number_format($vipDiscount, 0) }}%
                                </p>
                                <p class="text-xs text-emerald-800 dark:text-emerald-200 mt-1">
                                    {{ app()->getLocale() == 'ar' ? 'يتم احتساب خصم التوثيق أو الولاء تلقائياً.' : 'Verification or loyalty discount is applied automatically.' }}
                                </p>
                            </div>
                        @endif

                        @if ($isDiscountedInputPricing)
                            @php
                                $oldOfferAmount = old('offer_amount', 0);
                                $initialOfferFee = (($service->service_fee_type ?? 'fixed') === 'percentage')
                                    ? ((float) $oldOfferAmount) * ((float) $service->service_fee_value / 100)
                                    : (float) $service->service_fee_value;
                                $initialDiscountedPrice = max(0, round(((float) $oldOfferAmount) + $initialOfferFee - (((float) $oldOfferAmount) * ($serviceDiscountPercent / 100)), 2));
                            @endphp
                            <div class="space-y-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-3 text-sm text-slate-700 dark:text-slate-200">
                                <div class="space-y-1">
                                    <label for="offer_image" class="block text-sm font-semibold text-slate-800">صورة العرض</label>
                                    <input id="offer_image" name="offer_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="store-input" required>
                                    <x-input-error :messages="$errors->get('offer_image')" />
                                </div>

                                <div class="space-y-1">
                                    <label for="offer_amount" class="block text-sm font-semibold text-slate-800">قيمة العرض</label>
                                    <input id="offer_amount" name="offer_amount" type="number" step="0.01" min="0" value="{{ old('offer_amount', 0) }}" class="store-input" required>
                                    <x-input-error :messages="$errors->get('offer_amount')" />
                                </div>

                                <p class="text-sm">
                                    السعر الأساسي: <span class="font-semibold">{{ number_format((float) $oldOfferAmount, 2) }} USD</span><br>
                                    رسوم الخدمة: <span class="font-semibold">{{ number_format($initialOfferFee, 2) }} USD</span><br>
                                    Total = Price + Service Fee:
                                    <span class="font-semibold">{{ number_format(((float) $oldOfferAmount) + $initialOfferFee, 2) }} USD</span><br>
                                    المبلغ المدفوع:
                                    <span id="current-price" class="font-semibold text-emerald-700">{{ number_format($initialDiscountedPrice, 2) }}</span>
                                    <span id="price-currency">USD</span>
                                </p>
                                <input type="hidden" name="selected_price" id="selected-price-input" value="{{ number_format($initialDiscountedPrice, 2, '.', '') }}">
                                <p id="insufficient-message" class="mt-2 text-xs text-rose-600 hidden">{{ __('messages.insufficient_balance_msg') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ __('messages.held_amount_notice') }}</p>
                            </div>
                        @else
                            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-3 text-sm text-slate-700 dark:text-slate-200">
                                <p >{{ __('messages.current_price') }}:
                                    @if ($service->variants->count())
                                        @php
                                            // Get the first variant (which is auto-selected)
                                            $firstVariant = $service->variants->sortBy('sort_order')->first();
                                            $originalPrice = $firstVariant->price;
                                            $initialFee = (($firstVariant->service_fee_type ?? 'fixed') === 'percentage')
                                                ? $originalPrice * ((float) $firstVariant->service_fee_value / 100)
                                                : (float) $firstVariant->service_fee_value;
                                            $initialGross = $originalPrice + $initialFee;
                                            $displayPrice = $vipDiscount > 0 ? $initialGross * (1 - $vipDiscount / 100) : $initialGross;
                                        @endphp
                                        <div class="mb-2 space-y-1">
                                            <div class="flex items-center justify-between gap-3"><span>السعر الأساسي</span><span id="price-base">${{ number_format($originalPrice, 2) }}</span></div>
                                            <div class="flex items-center justify-between gap-3"><span>رسوم الخدمة</span><span id="price-fee">${{ number_format($initialFee, 2) }}</span></div>
                                            <div class="flex items-center justify-between gap-3 font-semibold"><span>Total = Price + Service Fee</span><span id="price-gross">${{ number_format($initialGross, 2) }}</span></div>
                                        </div>
                                        @if ($vipDiscount > 0)
                                            <span id="original-price" class="text-xs text-slate-500 line-through">${{ number_format($initialGross, 2) }}</span>
                                        @endif
                                        <span id="current-price" class="font-semibold text-emerald-700">${{ number_format($displayPrice, 2) }}</span>
                                    @elseif ($service->is_quantity_based)
                                        @php
                                            $initialBase = (float) $service->price_per_unit;
                                            $initialFee = (($service->service_fee_type ?? 'fixed') === 'percentage')
                                                ? $initialBase * ((float) $service->service_fee_value / 100)
                                                : (float) $service->service_fee_value;
                                            $initialGross = $initialBase + $initialFee;
                                            $displayPrice = $vipDiscount > 0 ? $initialGross * (1 - $vipDiscount / 100) : $initialGross;
                                        @endphp
                                        <div class="mb-2 space-y-1">
                                            <div class="flex items-center justify-between gap-3"><span>السعر الأساسي</span><span id="price-base">${{ number_format($initialBase, 2) }}</span></div>
                                            <div class="flex items-center justify-between gap-3"><span>رسوم الخدمة</span><span id="price-fee">${{ number_format($initialFee, 2) }}</span></div>
                                            <div class="flex items-center justify-between gap-3 font-semibold"><span>Total = Price + Service Fee</span><span id="price-gross">${{ number_format($initialGross, 2) }}</span></div>
                                        </div>
                                        @if ($vipDiscount > 0)
                                            <span id="original-price" class="text-xs text-slate-500 line-through">${{ number_format($initialGross, 2) }}</span>
                                        @endif
                                        <span id="current-price" class="font-semibold text-emerald-700">${{ number_format($displayPrice, 2) }}</span>
                                    @else
                                        @php
                                            $initialBase = (float) $service->price;
                                            $initialFee = (($service->service_fee_type ?? 'fixed') === 'percentage')
                                                ? $initialBase * ((float) $service->service_fee_value / 100)
                                                : (float) $service->service_fee_value;
                                            $initialGross = $initialBase + $initialFee;
                                            $displayPrice = $vipDiscount > 0 ? $initialGross * (1 - $vipDiscount / 100) : $initialGross;
                                        @endphp
                                        <div class="mb-2 space-y-1">
                                            <div class="flex items-center justify-between gap-3"><span>السعر الأساسي</span><span id="price-base">${{ number_format($initialBase, 2) }}</span></div>
                                            <div class="flex items-center justify-between gap-3"><span>رسوم الخدمة</span><span id="price-fee">${{ number_format($initialFee, 2) }}</span></div>
                                            <div class="flex items-center justify-between gap-3 font-semibold"><span>Total = Price + Service Fee</span><span id="price-gross">${{ number_format($initialGross, 2) }}</span></div>
                                        </div>
                                        @if ($vipDiscount > 0)
                                            <span id="original-price" class="text-xs text-slate-500 line-through">${{ number_format($initialGross, 2) }}</span>
                                        @endif
                                        <span id="current-price" class="font-semibold text-emerald-700">${{ number_format($displayPrice, 2) }}</span>
                                    @endif
                                    <span id="price-currency">USD</span>
                                </p>
                                <input type="hidden" name="selected_price" id="selected-price-input" value="{{ $displayPrice }}">
                                <input type="hidden" name="vip_discount" value="{{ $vipDiscount }}">
                                <p id="insufficient-message"
                                    class="mt-2 text-xs text-rose-600 {{ $isBaseInsufficient ? '' : 'hidden' }}">
                                    {{ __('messages.insufficient_balance_msg') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ __('messages.held_amount_notice') }}</p>
                            </div>
                        @endif

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

                        @if ($service->order_image_upload_enabled)
                            <div class="space-y-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-700/50">
                                <label for="order_image" class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                                    صورة مرفقة مع الطلب {{ $service->order_image_required ? '*' : '(اختياري)' }}
                                </label>
                                @if ($service->order_image_help_text)
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $service->order_image_help_text }}</p>
                                @endif
                                <input id="order_image" name="order_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="store-input" {{ $service->order_image_required ? 'required' : '' }}>
                                <p class="text-xs text-slate-500">JPG, PNG, WEBP - حتى 2MB</p>
                                <x-input-error :messages="$errors->get('order_image')" />
                            </div>
                        @endif

                        @php
                            $termsLink = '<a href="'.route('terms-of-use').'" class="font-semibold text-emerald-700 hover:underline dark:text-emerald-400">'.e(__('messages.terms_of_use')).'</a>';
                        @endphp

                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-4 py-3">
                            <label for="accept-terms-checkbox" class="flex items-start gap-3 text-sm text-slate-700 dark:text-slate-200">
                                <input
                                    id="accept-terms-checkbox"
                                    name="accept_terms"
                                    type="checkbox"
                                    value="1"
                                    class="mt-1 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                    @checked(old('accept_terms'))
                                >
                                <span class="leading-7">{!! __('messages.accept_terms_purchase_label', ['terms' => $termsLink]) !!}</span>
                            </label>
                            <x-input-error :messages="$errors->get('accept_terms')" />
                        </div>

                        @auth
                            <button id="purchase-button" type="submit"
                                data-processing-label="{{ __('messages.purchase_submitting') }}"
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

                    @if ($service->localized_seo_content)
                        <article class="prose prose-slate mt-8 max-w-none rounded-2xl border border-slate-200 bg-white p-5 text-sm leading-7 text-slate-700 dark:prose-invert dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            {!! $service->localized_seo_content !!}
                        </article>
                    @endif
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

            const daysElement = countdownElement.querySelector('[data-countdown-days]');
            const hoursElement = countdownElement.querySelector('[data-countdown-hours]');
            const minutesElement = countdownElement.querySelector('[data-countdown-minutes]');
            const secondsElement = countdownElement.querySelector('[data-countdown-seconds]');
            const endAtRaw = countdownElement.dataset.endAt;
            const endAt = endAtRaw ? new Date(endAtRaw).getTime() : Number.NaN;
            const setCountdown = ({ days = '--', hours = '--', minutes = '--', seconds = '--' }) => {
                if (daysElement) daysElement.textContent = days;
                if (hoursElement) hoursElement.textContent = hours;
                if (minutesElement) minutesElement.textContent = minutes;
                if (secondsElement) secondsElement.textContent = seconds;
            };

            if (Number.isNaN(endAt)) {
                setCountdown({});
                return;
            }

            const purchaseButton = document.getElementById('purchase-button');
            const format = (value) => String(value).padStart(2, '0');

            const render = () => {
                const remainingMs = endAt - Date.now();

                if (remainingMs <= 0) {
                    setCountdown({ days: '000', hours: '00', minutes: '00', seconds: '00' });

                    if (purchaseButton) {
                        purchaseButton.setAttribute('disabled', 'disabled');
                    }

                    return false;
                }

                const totalSeconds = Math.floor(remainingMs / 1000);
                const days = Math.floor(totalSeconds / (24 * 60 * 60));
                const hours = Math.floor((totalSeconds % (24 * 60 * 60)) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                setCountdown({
                    days: String(days).padStart(3, '0'),
                    hours: format(hours),
                    minutes: format(minutes),
                    seconds: format(seconds),
                });
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
            $purchasePageConfig = [
                'availableBalance' => (float) $availableBalance,
                'isDiscountedInputPricing' => $isDiscountedInputPricing,
                'serviceDiscountPercent' => (float) $serviceDiscountPercent,
                'hasVariants' => ! $isDiscountedInputPricing && $service->variants->count() > 0,
                'isQuantityBased' => ! $isDiscountedInputPricing && $service->is_quantity_based,
                'vipDiscount' => (float) $vipDiscount,
                'currentLocale' => str_replace('_', '-', app()->getLocale()),
                'isRtl' => app()->getLocale() === 'ar',
                'serviceName' => $service->localized_name,
                'servicePrice' => (float) $service->price,
                'serviceFeeType' => $service->service_fee_type ?? 'fixed',
                'serviceFeeValue' => (float) ($service->service_fee_value ?? 0),
                'selectPackageMessage' => $selectPackageMessage,
                'confirmationLabels' => [
                    'title' => __('messages.purchase_confirmation_title'),
                    'text' => __('messages.purchase_confirmation_text'),
                    'service' => __('messages.service'),
                    'package' => __('messages.package'),
                    'quantity' => __('messages.quantity'),
                    'price' => __('messages.price_label'),
                    'confirm' => __('messages.purchase_confirmation_confirm'),
                    'cancel' => __('messages.purchase_confirmation_cancel'),
                ],
            ];
        @endphp
        <script id="purchase-page-config" type="application/json">@json($purchasePageConfig)</script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const configElement = document.getElementById('purchase-page-config');
                if (!configElement) {
                    return;
                }

                const config = JSON.parse(configElement.textContent || '{}');
                const availableBalance = parseFloat(config.availableBalance || 0);
                const priceElement = document.getElementById('current-price');
                const originalPriceElement = document.getElementById('original-price');
                const priceCurrency = document.getElementById('price-currency');
                const priceInput = document.getElementById('selected-price-input');
                const insufficientMessage = document.getElementById('insufficient-message');
                const purchaseForm = document.getElementById('purchase-form');
                const purchaseButton = document.getElementById('purchase-button');
                const termsCheckbox = document.getElementById('accept-terms-checkbox');
                const variantInputs = document.querySelectorAll('input[name="variant_id"]');
                const quantityInput = document.getElementById('quantity-input');
                const offerAmountInput = document.getElementById('offer_amount');
                const countdownElement = document.querySelector('[data-limited-offer-countdown]');
                const isDiscountedInputPricing = Boolean(config.isDiscountedInputPricing);
                const serviceDiscountPercent = parseFloat(config.serviceDiscountPercent || 0);
                const hasVariants = Boolean(config.hasVariants);
                const isQuantityBased = Boolean(config.isQuantityBased);
                const vipDiscount = parseFloat(config.vipDiscount || 0);
                const currentLocale = config.currentLocale || 'en';
                const isRtl = Boolean(config.isRtl);
                const serviceName = config.serviceName || '';
                const servicePrice = parseFloat(config.servicePrice || 0);
                const serviceFeeType = config.serviceFeeType || 'fixed';
                const serviceFeeValue = parseFloat(config.serviceFeeValue || 0);
                const selectPackageMessage = config.selectPackageMessage || '';
                const confirmationLabels = config.confirmationLabels || {};
                const basePriceElement = document.getElementById('price-base');
                const feeElement = document.getElementById('price-fee');
                const grossElement = document.getElementById('price-gross');

                const escapeHtml = (value) => String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                const formatPrice = (price) => {
                    // Avoid scientific notation and handle small numbers
                    // If price < 0.01 and not 0, show more decimals
                    if (price > 0 && price < 0.01) {
                         return price.toFixed(12).replace(/\.?0+$/, "");
                    }
                    return price.toFixed(2);
                };

                const formatModalPrice = (price) => {
                    if (price === null || Number.isNaN(price)) {
                        return '';
                    }

                    try {
                        return new Intl.NumberFormat(currentLocale, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        }).format(price) + ' USD';
                    } catch (error) {
                        return formatPrice(price) + ' USD';
                    }
                };

                const getSelectedVariantLabel = () => {
                    const checked = document.querySelector('input[name="variant_id"]:checked');

                    return checked?.dataset.variantName || null;
                };

                const getSelectedPrice = () => {
                    if (isDiscountedInputPricing) {
                        const offerAmount = parseFloat(offerAmountInput?.value ?? '0');
                        if (Number.isNaN(offerAmount)) {
                            return null;
                        }

                        const normalizedOfferAmount = Math.max(0, offerAmount);
                        const feeAmount = serviceFeeType === 'percentage' ? normalizedOfferAmount * (serviceFeeValue / 100) : serviceFeeValue;
                        return Math.max(0, normalizedOfferAmount + feeAmount - (normalizedOfferAmount * (serviceDiscountPercent / 100)));
                    }

                    if (isQuantityBased && quantityInput) {
                        const quantity = parseInt(quantityInput.value) || 1;
                        const pricePerUnit = parseFloat(quantityInput.dataset.pricePerUnit);
                        const basePrice = quantity * pricePerUnit;
                        const feeAmount = serviceFeeType === 'percentage' ? basePrice * (serviceFeeValue / 100) : serviceFeeValue;
                        let totalPrice = basePrice + feeAmount;
                        if (vipDiscount > 0) {
                            totalPrice = totalPrice * (1 - vipDiscount / 100);
                        }
                        return totalPrice;
                    }

                    const checked = document.querySelector('input[name="variant_id"]:checked');
                    if (checked && checked.dataset.price) {
                        return parseFloat(checked.dataset.price);
                    }

                    const feeAmount = serviceFeeType === 'percentage' ? servicePrice * (serviceFeeValue / 100) : serviceFeeValue;
                    let price = hasVariants ? null : servicePrice + feeAmount;
                    if (price !== null && vipDiscount > 0) {
                        price = price * (1 - vipDiscount / 100);
                    }
                    return price;
                };

                const getOriginalPrice = () => {
                    if (isDiscountedInputPricing) {
                        const offerAmount = parseFloat(offerAmountInput?.value ?? '0');
                        if (Number.isNaN(offerAmount)) {
                            return null;
                        }

                        return Math.max(0, offerAmount);
                    }

                    const checked = document.querySelector('input[name="variant_id"]:checked');
                    if (checked && checked.dataset.grossPrice) {
                        return parseFloat(checked.dataset.grossPrice);
                    }

                    if (isQuantityBased && quantityInput) {
                        const quantity = parseInt(quantityInput.value) || 1;
                        const pricePerUnit = parseFloat(quantityInput.dataset.pricePerUnit);
                        const basePrice = quantity * pricePerUnit;
                        const feeAmount = serviceFeeType === 'percentage' ? basePrice * (serviceFeeValue / 100) : serviceFeeValue;
                        return basePrice + feeAmount;
                    }

                    return null;
                };

                const updateState = () => {
                    const price = getSelectedPrice();
                    const originalPrice = getOriginalPrice();
                    const checked = document.querySelector('input[name="variant_id"]:checked');
                    const countdownExpired = countdownElement
                        ? (countdownElement.dataset.endAt ? new Date(countdownElement.dataset.endAt).getTime() <= Date.now() : false)
                        : false;
                    
                    if (priceElement) {
                        if (price !== null) {
                            if (!Number.isNaN(price)) {
                                const normalizedPrice = Math.max(0, price);
                                priceElement.textContent = isDiscountedInputPricing
                                    ? formatPrice(normalizedPrice)
                                    : '$' + formatPrice(normalizedPrice);
                                if (priceCurrency) priceCurrency.classList.remove('hidden');
                                if (priceInput) {
                                    priceInput.value = isDiscountedInputPricing
                                        ? normalizedPrice.toFixed(2)
                                        : normalizedPrice.toFixed(12); // Send high precision to backend
                                }

                                if (!isDiscountedInputPricing) {
                                    let baseForBreakdown = hasVariants && checked ? parseFloat(checked.dataset.originalPrice || 0) : servicePrice;
                                    if (isQuantityBased && quantityInput) {
                                        baseForBreakdown = (parseInt(quantityInput.value) || 1) * parseFloat(quantityInput.dataset.pricePerUnit || 0);
                                    }
                                    const feeForBreakdown = hasVariants && checked
                                        ? parseFloat(checked.dataset.feeAmount || 0)
                                        : (serviceFeeType === 'percentage' ? baseForBreakdown * (serviceFeeValue / 100) : serviceFeeValue);
                                    const grossForBreakdown = baseForBreakdown + feeForBreakdown;
                                    if (basePriceElement) basePriceElement.textContent = '$' + formatPrice(baseForBreakdown);
                                    if (feeElement) feeElement.textContent = '$' + formatPrice(feeForBreakdown);
                                    if (grossElement) grossElement.textContent = '$' + formatPrice(grossForBreakdown);
                                }
                                
                                // Update original price if VIP discount is active
                                if (!isDiscountedInputPricing && vipDiscount > 0) {
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
                            priceElement.textContent = selectPackageMessage;
                            if (priceCurrency) priceCurrency.classList.add('hidden');
                            if (priceInput) priceInput.value = '';
                            if (originalPriceElement) originalPriceElement.classList.add('hidden');
                        }
                    }

                    if (!purchaseButton) {
                        return;
                    }

                    const needsBalance = price !== null && !Number.isNaN(price) && price > 0;
                    const insufficientBalance = needsBalance && availableBalance < price;
                    const hasAcceptedTerms = termsCheckbox ? termsCheckbox.checked : true;

                    if (countdownExpired || price === null || insufficientBalance || !hasAcceptedTerms) {
                        purchaseButton.setAttribute('disabled', 'disabled');
                        if (insufficientMessage) {
                            if (!countdownExpired && insufficientBalance) {
                                insufficientMessage.classList.remove('hidden');
                            } else {
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

                if (purchaseForm) {
                    purchaseForm.addEventListener('submit', (event) => {
                        if (purchaseForm.dataset.confirmed === 'true') {
                            return;
                        }

                        event.preventDefault();

                        const selectedPrice = getSelectedPrice();
                        const details = [
                            {
                                label: confirmationLabels.service,
                                value: serviceName,
                            },
                            hasVariants
                                ? {
                                    label: confirmationLabels.package,
                                    value: getSelectedVariantLabel(),
                                }
                                : null,
                            isQuantityBased && quantityInput
                                ? {
                                    label: confirmationLabels.quantity,
                                    value: quantityInput.value,
                                }
                                : null,
                            {
                                label: confirmationLabels.price,
                                value: formatModalPrice(selectedPrice),
                            },
                        ].filter((item) => item && item.value !== null && item.value !== '');

                        const detailsHtml = details.map((item) => `
                            <div class="purchase-confirmation-detail">
                                <span class="purchase-confirmation-detail-label">${escapeHtml(item.label)}</span>
                                <span class="purchase-confirmation-detail-value">${escapeHtml(item.value)}</span>
                            </div>
                        `).join('');

                        Swal.fire({
                            icon: 'question',
                            title: confirmationLabels.title,
                            html: `
                                <div dir="${isRtl ? 'rtl' : 'ltr'}" class="space-y-4 text-start">
                                    <p class="purchase-confirmation-text">${escapeHtml(confirmationLabels.text)}</p>
                                    <div class="purchase-confirmation-details">${detailsHtml}</div>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: confirmationLabels.confirm,
                            cancelButtonText: confirmationLabels.cancel,
                            reverseButtons: isRtl,
                            focusCancel: true,
                            buttonsStyling: false,
                            customClass: {
                                popup: 'purchase-confirmation-popup rounded-3xl p-6 text-start shadow-2xl',
                                title: 'purchase-confirmation-title text-xl font-bold',
                                htmlContainer: 'mt-3',
                                actions: 'mt-6 flex gap-3',
                                confirmButton: 'purchase-confirmation-confirm inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-semibold transition hover:brightness-105',
                                cancelButton: 'purchase-confirmation-cancel inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-semibold transition',
                            },
                        }).then((result) => {
                            if (!result.isConfirmed) {
                                return;
                            }

                            purchaseForm.dataset.confirmed = 'true';

                            if (purchaseButton) {
                                purchaseButton.setAttribute('disabled', 'disabled');
                                purchaseButton.textContent = purchaseButton.dataset.processingLabel || purchaseButton.textContent;
                            }

                            HTMLFormElement.prototype.submit.call(purchaseForm);
                        });
                    });
                }

                variantInputs.forEach((input) => {
                    input.addEventListener('change', updateState);
                });

                if (quantityInput) {
                    quantityInput.addEventListener('input', updateState);
                }

                if (offerAmountInput) {
                    offerAmountInput.addEventListener('input', updateState);
                }

                if (termsCheckbox) {
                    termsCheckbox.addEventListener('change', updateState);
                }

                updateState();
            });
        </script>

    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-button-bg]').forEach((element) => {
                element.style.backgroundColor = element.dataset.buttonBg;
            });

            document.querySelectorAll('[data-share-service]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.dataset.shareUrl;
                    const title = button.dataset.shareTitle;
                    const successMessage = button.dataset.shareSuccess;
                    const failureMessage = button.dataset.shareFailure;

                    try {
                        if (navigator.share) {
                            await navigator.share({ title, url });
                            return;
                        }

                        await navigator.clipboard.writeText(url);

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: successMessage,
                            showConfirmButton: false,
                            timer: 1800,
                        });
                    } catch (error) {
                        if (error?.name === 'AbortError') {
                            return;
                        }

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'error',
                            title: failureMessage,
                            showConfirmButton: false,
                            timer: 2200,
                        });
                    }
                });
            });
        });
    </script>
@endsection
