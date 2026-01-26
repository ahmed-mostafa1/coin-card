@props([
    'service',
    'href' => null,
    'searchTarget' => 'products',
    'subtitle' => null,
])

@php
    $price = $service->variants->count() ? $service->variants->min('price') : $service->price;
    $image = $service->image_path ? asset('storage/' . $service->image_path) : null;
    $subtitle ??= $service->category->name ?? '';
@endphp

<a href="{{ $href ?? route('services.show', $service->slug) }}"
    class="store-card group overflow-hidden"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $service->name }}"
    data-filter-alt="{{ $subtitle }}">
    <div class="relative">
        <div class="aspect-[4/5] overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $service->name }}" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]">
            @endif
        </div>
    </div>
    <div class="px-3 pb-4 pt-2 text-center">
        <div class="store-card-title">{{ $service->name }}</div>
        <div class="text-[13px] text-slate-500">{{ $subtitle }}</div>
        <div class="mt-1 text-[15px] font-semibold text-rose-600">$ {{ number_format($price, 2) }}</div>
    </div>
</a>
