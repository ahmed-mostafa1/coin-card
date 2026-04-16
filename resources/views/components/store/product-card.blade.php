@props([
    'service',
    'href' => null,
    'searchTarget' => 'products',
    'subtitle' => null,
    'layout' => 'default',
])

@php
    if ($service->variants->count()) {
        $price = $service->variants->min('price');
    } elseif ($service->is_quantity_based && $service->price_per_unit) {
        $price = $service->price_per_unit;
    } else {
        $price = $service->price;
    }

    $image = $service->image_path ? asset('storage/' . $service->image_path) : null;
    $subtitle ??= $service->category->localized_name ?? '';
    $isFeatureLayout = $layout === 'feature';
    $isCategoryLayout = $layout === 'category';
@endphp

<a href="{{ $service->is_active ? ($href ?? route('services.show', $service->slug)) : '#' }}"
    class="group relative block overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 {{ $isFeatureLayout || $isCategoryLayout ? 'bg-slate-800 shadow-md hover:shadow-lg' : 'bg-white dark:bg-slate-800 shadow-sm hover:shadow-md' }} transition-all duration-200 hover:-translate-y-0.5 {{ !$service->is_active ? 'opacity-70 grayscale cursor-not-allowed' : '' }}"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $service->localized_name }}"
    data-filter-alt="{{ $subtitle }}">

    <div class="{{ $isCategoryLayout ? 'aspect-square' : 'aspect-square' }} w-full overflow-hidden {{ $isCategoryLayout ? '' : 'bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800' }}">
        @if ($image)
            <img src="{{ $image }}"
                 alt="{{ $service->localized_name }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center {{ $isCategoryLayout ? 'bg-gradient-to-br from-slate-700 to-slate-900' : '' }}">
                <i class="fa-solid fa-box text-3xl {{ $isCategoryLayout ? 'text-slate-400' : 'text-slate-300 dark:text-slate-600' }}"></i>
            </div>
        @endif

        @if (!$service->is_active)
            <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                <span class="rounded-full bg-slate-900/80 px-2.5 py-1 text-xs font-bold text-white shadow -rotate-12">
                    {{ __('messages.unavailable') ?? 'Unavailable' }}
                </span>
            </div>
        @endif
    </div>

    @if ($isFeatureLayout || $isCategoryLayout)
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-2 px-3 pb-2.5 pt-6">
            <div class="min-w-0">
                <span class="block text-sm font-bold leading-tight text-white drop-shadow line-clamp-2">{{ $service->localized_name }}</span>
                @if ($subtitle && $isFeatureLayout)
                    <span class="mt-1 block truncate text-[11px] text-white/75">{{ $subtitle }}</span>
                @endif
            </div>

            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-xs text-white backdrop-blur-sm transition group-hover:bg-emerald-500">
                <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[10px]"></i>
            </span>
        </div>
    @else
        <div class="px-2 pt-2 pb-3 text-center">
            <p class="text-[13px] font-semibold text-slate-800 dark:text-slate-100 line-clamp-2 leading-snug">
                {{ $service->localized_name }}
            </p>
            @if ($subtitle)
                <p class="mt-0.5 truncate text-[11px] text-slate-400 dark:text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
</a>
