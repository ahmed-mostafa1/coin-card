@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
    <section class="grid gap-6 lg:grid-cols-2">
        <x-card class="p-10">
            <h1 class="text-3xl font-bold text-slate-900">{{ __('messages.professional_platform') }}</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                {{ __('messages.platform_desc') }}
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#categories"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition duration-200 hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cc-press">{{ __('messages.browse_services') }}</a>
                <a href="{{ route('deposit.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition duration-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cc-press">{{ __('messages.top_up_balance') }}</a>
            </div>
        </x-card>
        <x-card class="bg-white text-slate-900" :hover="false">
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('messages.reliable_experience') }}</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                {{ __('messages.experience_desc') }}
            </p>
            <div class="mt-6 grid gap-3 text-sm">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700">
                    {{ __('messages.dynamic_forms') }}</div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700">
                    {{ __('messages.balance_deduction') }}</div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700">
                    {{ __('messages.clear_history') }}</div>
            </div>
        </x-card>
    </section>

    <x-card class="mt-10" :hover="false" id="categories">
        <x-page-header :title="__('messages.available_categories')" :subtitle="__('messages.choose_category')" />
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                    class="group rounded-2xl border border-slate-200 p-4 cc-hover-lift">
                    <div class="overflow-hidden rounded-xl">
                        @if ($category->image_path)
                            <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}"
                                class="h-32 w-full object-cover transition duration-200 group-hover:scale-[1.02]">
                        @else
                            <div class="flex h-32 items-center justify-center bg-emerald-50 text-emerald-700">
                                {{ mb_substr($category->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700">{{ $category->name }}</h3>
                        <span
                            class="text-xs text-slate-400 transition group-hover:translate-x-0.5">{!! app()->getLocale() == 'ar' ? '&larr;' : '&rarr;' !!}</span>
                    </div>
                </a>
            @empty
                <x-empty-state :message="__('messages.no_categories')" />
            @endforelse
        </div>
    </x-card>

    <x-card class="mt-10" :hover="false">
        <x-page-header :title="__('messages.featured_services')" :subtitle="__('messages.discover_services')" />
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($services as $service)
                <a href="{{ route('services.show', $service->slug) }}"
                    class="group rounded-2xl border border-slate-200 p-4 cc-hover-lift">
                    <div class="overflow-hidden rounded-xl">
                        @if ($service->image_path)
                            <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}"
                                class="h-32 w-full object-cover transition duration-200 group-hover:scale-[1.02]">
                        @else
                            <div class="flex h-32 items-center justify-center bg-emerald-50 text-emerald-700">
                                {{ mb_substr($service->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-slate-800 group-hover:text-emerald-700">{{ $service->name }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $service->category->name }}</p>
                    <p class="mt-3 text-sm font-semibold text-emerald-700">
                        @if ($service->variants->count())
                            {{ __('messages.starts_from') }} {{ number_format($service->variants->min('price'), 2) }} USD
                        @else
                            {{ number_format($service->price, 2) }} USD
                        @endif
                    </p>
                </a>
            @empty
                <x-empty-state :message="__('messages.no_services')" />
            @endforelse
        </div>
    </x-card>
@endsection