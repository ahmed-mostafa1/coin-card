@extends('layouts.app')

@section('title', $category->name)
@section('mainWidth', 'w-full max-w-full')

@section('content')
    <div class="store-shell space-y-6">
        <x-store.hero :banners="$sharedBanners" :alt="$category->name" />

        <x-store.notice :text="$sharedTickerText" />

        @if ($hasChildren)
            <form method="GET" class="space-y-3">
                <x-store.search-bar :placeholder="__('messages.search_section_placeholder')" target="subcategories" :value="$search" />
                <x-store.search-bar placeholder="" />
            </form>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="subcategories">
                @forelse ($subcategories as $sub)
                    <x-store.category-card :title="$sub->name" :href="route('categories.show', $sub->slug)"
                        :image="$sub->image_path ? asset('storage/' . $sub->image_path) : null" searchTarget="subcategories" />
                @empty
                    <x-empty-state :message="__('messages.no_categories')" class="sm:col-span-2 lg:col-span-4" />
                @endforelse
            </div>
        @else
            <form method="GET" class="space-y-3">
                <x-store.search-bar :placeholder="__('messages.search_products_placeholder')" target="products" :value="$search" />
            </form>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5" data-filter-list="products">
                @forelse ($services as $service)
                    <x-store.product-card :service="$service" searchTarget="products" />
                @empty
                    <x-empty-state :message="__('messages.no_services_available')" class="sm:col-span-2 lg:col-span-5" />
                @endforelse
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
