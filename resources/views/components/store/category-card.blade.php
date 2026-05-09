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
    class="group block overflow-hidden rounded-xl border border-slate-50 bg-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-800"
    data-filter-item="{{ $searchTarget }}"
    data-filter-name="{{ $title }}"
    data-filter-alt="{{ $subtitle }}">

    <div class="aspect-square w-full overflow-hidden bg-gradient-to-br from-slate-50 to-slate-50 dark:from-slate-700 dark:to-slate-900">
        @if ($image)
            <img src="{{ $image }}"
                 alt="{{ $title }}"
                 width="480"
                 height="480"
                 loading="lazy"
                 decoding="async"
                 onerror="this.onerror=null;this.src='{{ $fallback }}';"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center">
                <i class="fa-solid fa-layer-group text-3xl text-slate-400"></i>
            </div>
        @endif
    </div>

    <div class="flex min-h-[4.25rem] flex-col items-center justify-center px-2 py-3 text-center">
        <span class="block text-[13px] font-bold leading-snug text-slate-800 line-clamp-2 dark:text-slate-50 sm:text-sm">{{ $title }}</span>
    </div>
</a>
