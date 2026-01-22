@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <x-card :hover="false">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if ($category->image_path)
                    <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}"
                        class="h-20 w-20 rounded-2xl object-cover">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                        {{ mb_substr($category->name, 0, 1) }}</div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $category->name }}</h1>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ __('messages.services_count', ['count' => $services->count()]) }}</p>
                </div>
            </div>
            <form method="GET" action="{{ route('categories.show', $category->slug) }}"
                class="flex items-center gap-2 text-sm">
                <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:w-64" />
                <x-button type="submit" variant="secondary">{{ __('messages.search_button') }}</x-button>
            </form>
        </div>

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
                    <p class="mt-2 text-sm font-semibold text-emerald-700">{{ number_format($service->price, 2) }} USD</p>
                </a>
            @empty
                <x-empty-state :message="__('messages.no_services_available')" />
            @endforelse
        </div>
    </x-card>
@endsection