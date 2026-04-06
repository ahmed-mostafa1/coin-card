@props(['text'])

@php
    $locale      = app()->getLocale();
    $displayText = $locale === 'en' && !empty($sharedTickerTextEn ?? '')
        ? $sharedTickerTextEn
        : ($text ?? $sharedTickerText ?? '');
@endphp

<div class="flex items-center gap-3 rounded-xl border border-amber-300 bg-gradient-to-r from-amber-50 to-yellow-50 dark:border-amber-700 dark:from-amber-950/40 dark:to-yellow-950/40 px-4 py-2.5 shadow-sm w-full overflow-hidden">
    {{-- Pulsing dot --}}
    <span class="relative flex-shrink-0 flex h-2.5 w-2.5">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
    </span>
    {{-- Scrolling text --}}
    <div class="relative flex-1 overflow-hidden whitespace-nowrap">
        <span class="store-ticker-track text-amber-800 dark:text-amber-300 font-semibold text-[13px]">{{ $displayText }}</span>
    </div>
</div>
