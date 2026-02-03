@props(['title', 'subtitle' => null, 'center' => false])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-4 ' . ($center ? 'justify-center text-center flex-col' : 'justify-between')]) }}>
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {{ $actions ?? '' }}
    </div>
</div>
