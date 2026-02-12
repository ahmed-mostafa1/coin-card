@props(['text'])

@php
    $locale = app()->getLocale();
    $displayText = $locale === 'en' && !empty($sharedTickerTextEn ?? '') 
        ? $sharedTickerTextEn 
        : ($text ?? $sharedTickerText ?? '');
@endphp

<div class="store-pill w-4/5 mx-auto">
    <span class="inline-block h-2 w-2 rounded-full bg-slate-400"></span>
    <div class="relative flex-1 overflow-hidden whitespace-nowrap">
        <span class="store-ticker-track">{{ $displayText }}</span>
    </div>
</div>
