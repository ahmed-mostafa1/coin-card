@props([
    'title',
    'href'         => '#',
    'image'        => null,
    'subtitle'     => null,
    'searchTarget' => 'categories',
])

@php
    $subtitle ??= __('messages.store_marketing_tag');
    $fallback  = asset('img/placeholder-category.jpg');
@endphp

<a href="{{ $href }}"
    class="group relative block overflow-hidden rounded-xl shadow-md border border-slate-200 dark:border-slate-700 bg-slate-800 transition-transform duration-200 hover:-translate-y-0.5 hover:shadow-lg"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $title }}"
    data-filter-alt="{{ $subtitle }}">

    {{-- 16:9 landscape image --}}
    <div class="aspect-video w-full overflow-hidden">
        @if ($image)
            <img src="{{ $image }}"
                 alt="{{ $title }}"
                 onerror="this.onerror=null;this.src='{{ $fallback }}';"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-700 to-slate-900">
                <i class="fa-solid fa-layer-group text-3xl text-slate-400"></i>
            </div>
        @endif
    </div>

    {{-- Gradient overlay + title --}}
    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

    <div class="absolute bottom-0 inset-x-0 flex items-end justify-between px-3 pb-2.5 pt-6">
        <span class="text-sm font-bold text-white drop-shadow line-clamp-2 leading-tight">{{ $title }}</span>
        <span class="flex-shrink-0 flex h-6 w-6 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white text-xs ms-2 transition group-hover:bg-emerald-500">
            <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[10px]"></i>
        </span>
    </div>
</a>
