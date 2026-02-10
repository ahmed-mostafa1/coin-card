@extends('layouts.app')

@section('title', __('messages.add_field'))
@section('mainWidth', 'max-w-3xl w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.add_field') }}</h1>
            <a href="{{ route('admin.agency-request-fields.index') }}" class="text-sm text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">
                <i class="fa-solid fa-arrow-right ml-1"></i>
                {{ __('messages.back_to_list') }}
            </a>
        </div>

        <form method="POST" action="{{ route('admin.agency-request-fields.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="label" :value="__('messages.field_label_ar')" />
                    <x-text-input id="label" name="label" type="text" :value="old('label')" required />
                    <x-input-error :messages="$errors->get('label')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="label_en" :value="__('messages.field_label_en')" />
                    <x-text-input id="label_en" name="label_en" type="text" :value="old('label_en')" />
                    <x-input-error :messages="$errors->get('label_en')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="name_key" :value="__('messages.field_key')" />
                    <x-text-input id="name_key" name="name_key" type="text" dir="ltr" :value="old('name_key')" required />
                    <x-input-error :messages="$errors->get('name_key')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="type" :value="__('messages.field_type')" />
                    <x-select id="type" name="type" required>
                        <option value="text" @selected(old('type', 'text') === 'text')>{{ __('messages.field_type_text') }}</option>
                        <option value="textarea" @selected(old('type') === 'textarea')>{{ __('messages.field_type_textarea') }}</option>
                        <option value="number" @selected(old('type') === 'number')>{{ __('messages.field_type_number') }}</option>
                        <option value="email" @selected(old('type') === 'email')>{{ __('messages.field_type_email') }}</option>
                        <option value="tel" @selected(old('type') === 'tel')>{{ __('messages.field_type_tel') }}</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="placeholder" :value="__('messages.placeholder_ar_optional')" />
                    <x-text-input id="placeholder" name="placeholder" type="text" :value="old('placeholder')" />
                    <x-input-error :messages="$errors->get('placeholder')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="placeholder_en" :value="__('messages.placeholder_en_optional')" />
                    <x-text-input id="placeholder_en" name="placeholder_en" type="text" :value="old('placeholder_en')" />
                    <x-input-error :messages="$errors->get('placeholder_en')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
                    <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                    <input id="is_required" name="is_required" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600" {{ old('is_required', true) ? 'checked' : '' }}>
                    <label for="is_required">{{ __('messages.required_field') }}</label>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
                <a href="{{ route('admin.agency-request-fields.index') }}" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
