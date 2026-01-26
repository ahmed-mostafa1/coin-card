@props(['image' => null, 'alt' => '', 'height' => 'h-[260px]'])

<div class="overflow-hidden rounded-2xl border border-slate-300 bg-gradient-to-tr from-slate-800 via-slate-700 to-slate-600 shadow-md {{ $height }}">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $alt }}" class="h-full w-full object-cover">
    @endif
</div>
