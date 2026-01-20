@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-4']) }}>
    <div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-2 text-sm text-slate-600">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {{ $actions ?? '' }}
    </div>
</div>
