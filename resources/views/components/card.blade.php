@props(['hover' => true])

@php
    $base = 'rounded-2xl border border-slate-200 bg-white p-6 shadow-sm';
    $hoverClass = $hover ? 'cc-hover-lift' : '';
@endphp

<div {{ $attributes->merge(['class' => trim($base.' '.$hoverClass)]) }}>
    {{ $slot }}
</div>
