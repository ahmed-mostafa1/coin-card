@props(['text'])

<div class="store-pill">
    <span class="inline-block h-2 w-2 rounded-full bg-slate-400"></span>
    <div class="relative flex-1 overflow-hidden">
        <span class="store-ticker-track">{{ $text }}</span>
    </div>
</div>
