@props(['message'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center']) }}>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <p class="text-sm text-slate-600">{{ $message }}</p>
    {{ $slot ?? '' }}
</div>
