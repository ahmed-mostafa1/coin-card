@extends('layouts.app')

@php
    $categoryPreviewNames = ($hasChildren ? $subcategories : $services)
        ->take(4)
        ->pluck('localized_name')
        ->filter()
        ->implode('، ');
    $categoryTitle = app()->getLocale() === 'ar'
        ? $category->localized_name.' - خدمات وبطاقات رقمية'
        : $category->localized_name.' - Digital services and top ups';
    $categoryDescription = $categoryPreviewNames !== ''
        ? (
            app()->getLocale() === 'ar'
                ? 'تصفح قسم '.$category->localized_name.' واكتشف أبرز الخيارات المتاحة مثل '.$categoryPreviewNames.'.'
                : 'Browse '.$category->localized_name.' and discover featured options like '.$categoryPreviewNames.'.'
        )
        : (
            app()->getLocale() === 'ar'
                ? 'تصفح خدمات وبطاقات '.$category->localized_name.' مع خيارات رقمية محدثة وأسعار واضحة.'
                : 'Browse '.$category->localized_name.' digital services with updated options and clear pricing.'
        );
    $categoryDescription = \Illuminate\Support\Str::limit(strip_tags($categoryDescription), 160, '');
    $categoryImage = $category->image_path ? asset('storage/'.$category->image_path) : asset('img/placeholder-category.jpg');
    $breadcrumbItems = array_values(array_filter([
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home',
            'item' => route('home'),
        ],
        $category->parent ? [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $category->parent->localized_name,
            'item' => route('categories.show', $category->parent->slug),
        ] : null,
        [
            '@type' => 'ListItem',
            'position' => $category->parent ? 3 : 2,
            'name' => $category->localized_name,
            'item' => route('categories.show', $category->slug),
        ],
    ]));
    $categorySchema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $category->localized_name,
        'url' => route('categories.show', $category->slug),
        'description' => $categoryDescription,
        'inLanguage' => app()->getLocale(),
    ];
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ];
@endphp

@section('title', $categoryTitle)
@section('meta_description', $categoryDescription)
@section('meta_canonical', route('categories.show', $category->slug))
@section('meta_image', $categoryImage)
@section('meta_robots', $search ? 'noindex,follow' : 'index,follow')
@section('mainWidth', 'w-[85%] mx-auto')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($categorySchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <div class="store-shell space-y-4">

        {{-- Compact category header (replaces full hero slider) --}}
        <div class="flex items-center gap-3 px-3 pt-1">
            @if($category->image_path)
                <img src="{{ asset('storage/' . $category->image_path) }}"
                     alt="{{ $category->localized_name }}"
                     class="h-12 w-12 flex-shrink-0 rounded-full object-cover border-2 border-emerald-200 dark:border-emerald-800 shadow-sm">
            @else
                <div class="h-12 w-12 flex-shrink-0 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-emerald-600 dark:text-emerald-400"></i>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800 dark:text-slate-100 leading-tight truncate">{{ $category->localized_name }}</h1>
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    <a href="{{ route('home') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition">{{ __('messages.home') }}</a>
                    @if($category->parent)
                        <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[9px]"></i>
                        <a href="{{ route('categories.show', $category->parent->slug) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition truncate">{{ $category->parent->localized_name }}</a>
                    @endif
                    <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[9px]"></i>
                    <span class="truncate text-slate-700 dark:text-slate-300 font-medium">{{ $category->localized_name }}</span>
                </div>
            </div>
        </div>

        <div class="w-full px-3">
            <form method="GET">
                <x-store.search-bar
                    :placeholder="$hasChildren ? __('messages.search_section_placeholder') : __('messages.search_products_placeholder')"
                    :target="$hasChildren ? 'subcategories' : 'products'" :value="$search" />
            </form>
        </div>

        @if ($hasChildren)
            <div class="w-full px-3">
                <div class="grid gap-2.5 sm:gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="subcategories">
                    @forelse ($subcategories as $sub)
                        <x-store.category-card :title="$sub->localized_name" :href="route('categories.show', $sub->slug)"
                            :image="$sub->image_path ? asset('storage/' . $sub->image_path) : null" searchTarget="subcategories" />
                    @empty
                        <x-empty-state :message="__('messages.no_categories')" class="col-span-full" />
                    @endforelse
                </div>
            </div>
        @else
            <div class="w-full px-3">
                <div class="grid gap-2.5 sm:gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="products">
                    @forelse ($services as $service)
                        <x-store.product-card :service="$service" searchTarget="products" layout="category" />
                    @empty
                        <x-empty-state :message="__('messages.no_services_available')" class="col-span-full" />
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterItems = () => {
                document.querySelectorAll('[data-filter-target]').forEach((input) => {
                    const term = (input.value || '').toLowerCase().trim();
                    const target = input.dataset.filterTarget;
                    document.querySelectorAll(`[data-filter-item=\"${target}\"]`).forEach((card) => {
                        const haystack = `${card.dataset.filterName || ''} ${card.dataset.filterAlt || ''}`.toLowerCase();
                        card.classList.toggle('hidden', term && !haystack.includes(term));
                    });
                });
            };

            document.querySelectorAll('[data-filter-target]').forEach((input) => {
                input.addEventListener('input', filterItems);
            });

            filterItems();
        });
    </script>
@endsection
