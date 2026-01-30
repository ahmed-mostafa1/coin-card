@extends('layouts.app')

@section('title', __('messages.add_field'))

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700">{{ __('messages.add_field_for_service') }}</h1>
            <a href="{{ route('admin.services.edit', $service) }}" class="text-sm text-emerald-700">{{ __('messages.back_to_service') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.services.fields.store', $service) }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="type" :value="__('messages.field_type')" />
                <select id="type" name="type" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                    <option value="text" @selected(old('type') === 'text')>{{ __('messages.field_type_text') }}</option>
                    <option value="textarea" @selected(old('type') === 'textarea')>{{ __('messages.field_type_textarea') }}</option>
                </select>
                <x-input-error :messages="$errors->get('type')" />
            </div>

            <div>
                <x-input-label for="label" :value="__('messages.field_label_ar')" />
                <x-text-input id="label" name="label" type="text" :value="old('label')" required />
                <x-input-error :messages="$errors->get('label')" />
            </div>

            <div>
                <x-input-label for="label_en" :value="__('messages.field_label_en')" />
                <x-text-input id="label_en" name="label_en" type="text" :value="old('label_en')" />
                <x-input-error :messages="$errors->get('label_en')" />
            </div>

            <div>
                <x-input-label for="name_key" :value="__('messages.field_key')" />
                <x-text-input id="name_key" name="name_key" type="text" :value="old('name_key')" required />
                <x-input-error :messages="$errors->get('name_key')" />
            </div>

            <div>
                <x-input-label for="placeholder" :value="__('messages.placeholder_ar_optional')" />
                <x-text-input id="placeholder" name="placeholder" type="text" :value="old('placeholder')" />
            </div>

            <div>
                <x-input-label for="placeholder_en" :value="__('messages.placeholder_en_optional')" />
                <x-text-input id="placeholder_en" name="placeholder_en" type="text" :value="old('placeholder_en')" />
            </div>

            <div>
                <x-input-label for="validation_rules" :value="__('messages.validation_rules_optional')" />
                <x-text-input id="validation_rules" name="validation_rules" type="text" :value="old('validation_rules')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_required" name="is_required" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_required">{{ __('messages.required_field') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
            </div>
        </form>
    </div>
@endsection
