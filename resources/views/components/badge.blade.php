@props(['type' => 'default'])

@php
    $styles = [
        'pending' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400',
        'approved' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400',
        'rejected' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400',
        'new' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400',
        'processing' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400',
        'done' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400',
        'default' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$styles[$type]]) }}>
    {{ $slot }}
</span>
