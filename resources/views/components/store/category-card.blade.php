@props([
    'title',
    'href' => '#',
    'image' => null,
    'subtitle' => null,
    'searchTarget' => 'categories',
])

@php
    $subtitle ??= __('messages.store_marketing_tag');
    $fallback = asset('img/placeholder-category.jpg');
@endphp

<a href="{{ $href }}"
    class="store-card group overflow-hidden"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $title }}"
    data-filter-alt="{{ $subtitle }}">
    <div class="relative">
        <div class="aspect-[16/9] sm:aspect-[1/1] overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300">
            @if ($image)
                <img src="{{ $image }}" 
                     alt="{{ $title }}" 
                     onerror="this.onerror=null;this.src='{{ $fallback }}';"
                     class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]">
            @else
                <div class="flex h-full w-full items-center justify-center">
                    <i class="fa-solid fa-image text-4xl text-slate-400"></i>
                </div>
            @endif
        </div>
    </div>
    <div class="px-2 pb-3 pt-1.5 sm:px-3 sm:pb-4 sm:pt-2 text-center">
        <div class="store-card-title break-words line-clamp-2">{{ $title }}</div>
    </div>
</a>
