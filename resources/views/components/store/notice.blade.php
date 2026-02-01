@props(['text'])

<div class="store-pill w-full sm:w-4/5 sm:mx-auto">
    <span class="inline-block h-2 w-2 rounded-full bg-slate-400"></span>
    <div class="relative flex-1 overflow-hidden whitespace-nowrap">
        <span class="store-ticker-track">{{ $text }}</span>
    </div>
</div>
