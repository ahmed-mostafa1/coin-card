@props([
    'service',
    'href' => null,
    'searchTarget' => 'products',
    'subtitle' => null,
])

@php
    $price = $service->variants->count() ? $service->variants->min('price') : $service->price;
    $image = $service->image_path ? asset('storage/' . $service->image_path) : null;
    $subtitle ??= $service->category->localized_name ?? '';
@endphp

<a href="{{ $service->is_active ? ($href ?? route('services.show', $service->slug)) : '#' }}"
    class="store-card group overflow-hidden {{ !$service->is_active ? 'opacity-75 grayscale cursor-not-allowed' : '' }}"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $service->localized_name }}"
    data-filter-alt="{{ $subtitle }}">
    <div class="relative">
        <div class="aspect-[1/1] overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $service->localized_name }}" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]">
            @endif
            @if(!$service->is_active)
                <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                    <span class="bg-slate-800/80 text-white px-3 py-1 rounded-full text-xs font-bold transform -rotate-12">
                        غير متاحة حاليا
                    </span>
                </div>
            @endif
        </div>
    </div>
    <div class="px-2 pb-3 pt-1.5 sm:px-3 sm:pb-4 sm:pt-2 text-center">
        <div class="store-card-title break-words line-clamp-2">{{ $service->localized_name }}</div>
        <!-- <div class="text-[13px] text-slate-500">{{ $subtitle }}</div> -->
        <!-- <div class="mt-1 text-[15px] font-semibold text-rose-600">$ {{ number_format($price, 2) }}</div> -->
    </div>
</a>
