@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
    <div class="store-shell space-y-6">
        <x-store.hero :banners="$sharedBanners" :alt="__('messages.home')" />

        <x-store.notice :text="$sharedTickerText" />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-filter-list="categories">
            @forelse ($categories as $category)
                <x-store.category-card :title="$category->localized_name" :href="route('categories.show', $category->slug)"
                    :image="$category->image_path ? asset('storage/' . $category->image_path) : null"
                    searchTarget="categories" />
            @empty
                <x-empty-state :message="__('messages.no_categories')" class="sm:col-span-2 lg:col-span-4" />
            @endforelse
        </div>

        <div class="grid gap-4 pt-4 text-center text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-2 lg:grid-cols-4">
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-amber-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 7h18M6 7l2 12h8l2-12M9 11h6M9 15h6M8 7l1-3h6l1 3" />
                </svg>
                <p class="text-sm font-semibold text-amber-600 dark:text-amber-500">{{ __('messages.programming_design') }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('messages.service_design_desc') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-emerald-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M7 15h2M7 6h10M6 18h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Z" />
                </svg>
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-500">{{ __('messages.easy_payment') }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('messages.payment_desc') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-orange-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="m12 2 7 7-7 7-7-7 7-7Zm0 0v20" />
                </svg>
                <p class="text-sm font-semibold text-orange-600 dark:text-orange-500">{{ __('messages.fast_reliable') }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('messages.fast_process_desc') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-emerald-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 3 5 6v6c0 5 3.5 7.5 7 9 3.5-1.5 7-4 7-9V6l-7-3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 12 2 2 4-4" />
                </svg>
                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-500">{{ __('messages.guarantee') }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('messages.guarantee_desc') }}</p>
            </div>
        </div>

        <div class="store-card border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 p-5 text-sm leading-7 text-slate-700 dark:text-slate-300">
            {{ __('messages.store_description') }}
            <a href="{{ route('about') }}" class="font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('messages.contact_us') }}</a>.
        </div>
    </div>
@endsection
