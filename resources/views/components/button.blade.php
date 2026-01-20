@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition duration-200 motion-reduce:transition-none focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60';

    $variants = [
        'primary' => 'bg-emerald-600 text-white hover:brightness-105 focus:ring-emerald-500',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:ring-emerald-500',
        'danger' => 'bg-rose-600 text-white hover:brightness-105 focus:ring-rose-500',
        'ghost' => 'text-emerald-700 hover:bg-emerald-50 focus:ring-emerald-500',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes.' cc-press']) }}>
    {{ $slot }}
</button>
