@props([
    'title',
    'href' => '#',
    'image' => null,
    'subtitle' => null,
    'searchTarget' => 'categories',
])

@php
    $subtitle ??= __('messages.store_marketing_tag');
@endphp

<a href="{{ $href }}"
    class="store-card group overflow-hidden"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $title }}"
    data-filter-alt="{{ $subtitle }}">
    <div class="relative">
        <div class="aspect-[1/1] overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]">
            @endif
        </div>
    </div>
    <div class="store-card-footer">{{ $subtitle }}</div>
    <div class="px-3 pb-4 pt-2 text-center">
        <div class="store-card-title">{{ $title }}</div>
    </div>
</a>
