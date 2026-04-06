@props([
    'service',
    'href'         => null,
    'searchTarget' => 'products',
    'subtitle'     => null,
])

@php
    if ($service->variants->count()) {
        $price = $service->variants->min('price');
    } elseif ($service->is_quantity_based && $service->price_per_unit) {
        $price = $service->price_per_unit;
    } else {
        $price = $service->price;
    }

    $image    = $service->image_path ? asset('storage/' . $service->image_path) : null;
    $subtitle ??= $service->category->localized_name ?? '';
@endphp

<a href="{{ $service->is_active ? ($href ?? route('services.show', $service->slug)) : '#' }}"
    class="group relative block overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ !$service->is_active ? 'opacity-70 grayscale cursor-not-allowed' : '' }}"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $service->localized_name }}"
    data-filter-alt="{{ $subtitle }}">

    {{-- Square image --}}
    <div class="aspect-square w-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800">
        @if ($image)
            <img src="{{ $image }}"
                 alt="{{ $service->localized_name }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center">
                <i class="fa-solid fa-box text-3xl text-slate-300 dark:text-slate-600"></i>
            </div>
        @endif

        {{-- Unavailable overlay --}}
        @if (!$service->is_active)
            <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                <span class="bg-slate-900/80 text-white px-2.5 py-1 rounded-full text-xs font-bold -rotate-12 shadow">
                    {{ __('messages.unavailable') ?? 'غير متاحة' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Price badge top-start --}}
    @if ($price)
        <div class="absolute top-2 start-2 rounded-full bg-emerald-600 px-2 py-0.5 text-[11px] font-bold text-white shadow-sm" dir="ltr">
            ${{ number_format($price, 2) }}
        </div>
    @endif

    {{-- Title --}}
    <div class="px-2 pt-2 pb-3 text-center">
        <p class="text-[13px] font-semibold text-slate-800 dark:text-slate-100 line-clamp-2 leading-snug">
            {{ $service->localized_name }}
        </p>
        @if ($subtitle)
            <p class="mt-0.5 text-[11px] text-slate-400 dark:text-slate-500 truncate">{{ $subtitle }}</p>
        @endif
    </div>
</a>
