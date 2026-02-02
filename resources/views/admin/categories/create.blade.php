@extends('layouts.app')

@section('title', __('messages.add_category'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.add_category') }}</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('messages.category_name_ar')" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="name_en" :value="__('messages.category_name_en')" />
                <x-text-input id="name_en" name="name_en" type="text" :value="old('name_en')" />
                <x-input-error :messages="$errors->get('name_en')" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('messages.slug_optional')" />
                <x-text-input id="slug" name="slug" type="text" :value="old('slug')" />
                <x-input-error :messages="$errors->get('slug')" />
            </div>

            <div>
                <x-input-label for="parent_id" :value="__('messages.parent_category_optional')" />
                <select id="parent_id" name="parent_id"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">{{ __('messages.root_category_option') }}</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('parent_id')" />
            </div>

            <div>
                <x-input-label for="image" :value="__('messages.image_optional')" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                <x-input-error :messages="$errors->get('image')" />
                <p class="mt-1 text-xs text-slate-500">{{ __('messages.recommended_size') }}: 500x500 px</p>
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">{{ __('messages.activate_category') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-500">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection