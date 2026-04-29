@props(['title', 'subtitle' => null, 'center' => false])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 ' . ($center ? 'items-center justify-center text-center' : 'sm:flex-row sm:items-center sm:justify-between')]) }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap sm:items-center">
        {{ $actions ?? '' }}
    </div>
</div>
