@props(['hover' => true])

@php
    $base = 'rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm';
    $hoverClass = $hover ? 'cc-hover-lift' : '';
@endphp

<div {{ $attributes->merge(['class' => trim($base.' '.$hoverClass)]) }}>
    {{ $slot }}
</div>
