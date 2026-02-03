@extends('layouts.app')

@section('title', __('messages.edit_banner'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.edit_banner') }}</h1>

        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="title" :value="__('messages.banner_title_ar_optional')" />
                <x-text-input id="title" name="title" type="text" :value="old('title', $banner->title)" />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="title_en" :value="__('messages.banner_title_en_optional')" />
                <x-text-input id="title_en" name="title_en" type="text" :value="old('title_en', $banner->title_en)" />
                <x-input-error :messages="$errors->get('title_en')" />
            </div>

            <div>
                <x-input-label for="image" :value="__('messages.image_replaceable')" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-info-circle ml-1"></i>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-info-circle ml-1"></i>
                    {{ __('messages.recommended_size') }}: 1400x400 px
                </p>
                </p>
                <x-input-error :messages="$errors->get('image')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $banner->is_active ? 'checked' : '' }}>
                <label for="is_active">{{ __('messages.activate_banner') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $banner->sort_order)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.update') }}</x-primary-button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-500">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection