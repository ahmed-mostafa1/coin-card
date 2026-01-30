@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
    <div class="store-shell space-y-4 sm:space-y-6">
        <x-store.hero :banners="$sharedBanners" :alt="__('messages.home')" />

        <x-store.notice :text="$sharedTickerText" />

        <div class="w-full sm:w-4/5 mx-auto">
            <div class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="categories">
                @forelse ($categories as $category)
                    <x-store.category-card :title="$category->localized_name" :href="route('categories.show', $category->slug)"
                        :image="$category->image_path ? asset('storage/' . $category->image_path) : null"
                        searchTarget="categories" />
                @empty
                    <x-empty-state :message="__('messages.no_categories')" class="col-span-2 lg:col-span-4" />
                @endforelse
            </div>
        </div>

        <div class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 pt-2 text-center text-sm text-slate-700 dark:text-slate-300">
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p1.webp') }}" alt="{{ __('messages.programming_design') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.programming_design') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p2.webp') }}" alt="{{ __('messages.easy_payment') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.easy_payment') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p3.webp') }}" alt="{{ __('messages.fast_reliable') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.fast_reliable') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p4.webp') }}" alt="{{ __('messages.guarantee') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.guarantee') }}</p>
            </div>
        </div>

        <div class="store-card border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 p-4 text-base leading-6 text-slate-700 dark:text-slate-300 sm:p-5 sm:text-lg sm:leading-7 break-words">
            {{ __('messages.store_description') }}
            <a href="{{ route('about') }}" class="font-semibold text-orange-700 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300">{{ __('messages.contact_us') }}</a>.
        </div>
    </div>
@endsection
