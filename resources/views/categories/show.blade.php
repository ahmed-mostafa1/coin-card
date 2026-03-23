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
@section('mainWidth', 'w-full max-w-full')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($categorySchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <div class="store-shell space-y-6">
        <div class="w-[95%] md:w-[80%] mx-auto">
            <x-store.hero :banners="$sharedBanners" :alt="$category->localized_name" />
        </div>

        <div class="w-[95%] md:w-[80%] mx-auto">
            <x-store.notice :text="$sharedTickerText" />
        </div>

        <div class="w-full px-3 lg:w-4/5 lg:mx-auto">
            <form method="GET">
                <x-store.search-bar
                    :placeholder="$hasChildren ? __('messages.search_section_placeholder') : __('messages.search_products_placeholder')"
                    :target="$hasChildren ? 'subcategories' : 'products'" :value="$search" />
            </form>
        </div>

        @if ($hasChildren)
            <div class="w-full px-3 lg:w-4/5 lg:mx-auto">
                <div class="grid gap-2 sm:gap-3 lg:gap-4 grid-cols-2 lg:grid-cols-4" data-filter-list="subcategories">
                    @forelse ($subcategories as $sub)
                        <x-store.category-card :title="$sub->localized_name" :href="route('categories.show', $sub->slug)"
                            :image="$sub->image_path ? asset('storage/' . $sub->image_path) : null" searchTarget="subcategories" />
                    @empty
                        <x-empty-state :message="__('messages.no_categories')" class="col-span-2 lg:col-span-4" />
                    @endforelse
                </div>
            </div>
        @else
            <div class="w-full px-3 lg:w-4/5 lg:mx-auto">
                <div class="grid gap-2 sm:gap-3 lg:gap-4 grid-cols-2 lg:grid-cols-5" data-filter-list="products">
                    @forelse ($services as $service)
                        <x-store.product-card :service="$service" searchTarget="products" />
                    @empty
                        <x-empty-state :message="__('messages.no_services_available')" class="col-span-2 lg:col-span-5" />
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
