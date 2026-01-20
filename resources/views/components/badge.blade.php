@props(['type' => 'default'])

@php
    $styles = [
        'pending' => 'bg-amber-100 text-amber-700',
        'approved' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-rose-100 text-rose-700',
        'new' => 'bg-amber-100 text-amber-700',
        'processing' => 'bg-blue-100 text-blue-700',
        'done' => 'bg-emerald-100 text-emerald-700',
        'default' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$styles[$type]]) }}>
    {{ $slot }}
</span>
