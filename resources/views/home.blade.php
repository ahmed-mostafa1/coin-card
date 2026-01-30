@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
    <div class="store-shell space-y-6">
        <x-store.hero :banners="$sharedBanners" :alt="__('messages.home')" />

        <x-store.notice :text="$sharedTickerText" />

        <div class="w-full sm:w-4/5 mx-auto">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="categories">
                @forelse ($categories as $category)
                    <x-store.category-card :title="$category->localized_name" :href="route('categories.show', $category->slug)"
                        :image="$category->image_path ? asset('storage/' . $category->image_path) : null"
                        searchTarget="categories" />
                @empty
                    <x-empty-state :message="__('messages.no_categories')" class="sm:col-span-2 lg:col-span-4" />
                @endforelse
            </div>
        </div>

        <div class="grid gap-4 pt-4 text-center text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-2 lg:grid-cols-4">
            <div class="store-card flex flex-col items-center gap-3 p-6">
                <img src="{{ asset('img/home/p1.webp') }}" alt="{{ __('messages.programming_design') }}" class="h-16 w-16 object-contain">
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-500">{{ __('messages.programming_design') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-3 p-6">
                <img src="{{ asset('img/home/p2.webp') }}" alt="{{ __('messages.easy_payment') }}" class="h-16 w-16 object-contain">
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-500">{{ __('messages.easy_payment') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-3 p-6">
                <img src="{{ asset('img/home/p3.webp') }}" alt="{{ __('messages.fast_reliable') }}" class="h-16 w-16 object-contain">
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-500">{{ __('messages.fast_reliable') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-3 p-6">
                <img src="{{ asset('img/home/p4.webp') }}" alt="{{ __('messages.guarantee') }}" class="h-16 w-16 object-contain">
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-500">{{ __('messages.guarantee') }}</p>
            </div>
        </div>

        <div class="store-card border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 p-5 text-lg leading-7 text-slate-700 dark:text-slate-300">
            {{ __('messages.store_description') }}
            <a href="{{ route('about') }}" class="font-semibold text-orange-700 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300">{{ __('messages.contact_us') }}</a>.
        </div>
    </div>
@endsection
