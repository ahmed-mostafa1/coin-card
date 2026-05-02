@props(['text' => null])

@php
    $displayText = app()->getLocale() === 'en' && !empty($sharedTickerTextEn ?? '')
        ? $sharedTickerTextEn
        : ($text ?? $sharedTickerText ?? '');
@endphp

@if(filled($displayText))
    <section {{ $attributes->merge(['class' => 'home-ticker']) }}>
        <div class="home-ticker__label">
            <i class="fa-solid fa-bullhorn"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'تنبيه' : 'Notice' }}</span>
        </div>
        <div class="home-ticker__viewport min-w-0 flex-1 overflow-hidden">
            <div class="store-ticker-track" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                {{ $displayText }}
            </div>
        </div>
    </section>
@endif
