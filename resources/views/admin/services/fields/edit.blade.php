@extends('layouts.app')

@section('title', __('messages.edit_field'))

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.edit_field') }}</h1>
                <a href="{{ route('admin.services.edit', $service) }}" class="text-sm text-emerald-700 dark:text-emerald-400">{{ __('messages.back_to_service') }}</a>
            </div>

            @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.services.fields.update', [$service, $field]) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="type" :value="__('messages.field_type')" />
                    <x-select id="type" name="type" required>
                        <option value="text" @selected(old('type', $field->type) === 'text')>{{ __('messages.field_type_text') }}</option>
                        <option value="textarea" @selected(old('type', $field->type) === 'textarea')>{{ __('messages.field_type_textarea') }}</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('type')" />
                </div>

                <div>
                    <x-input-label for="label" :value="__('messages.field_label_ar')" />
                    <x-text-input id="label" name="label" type="text" :value="old('label', $field->label)" required />
                    <x-input-error :messages="$errors->get('label')" />
                </div>

                <div>
                    <x-input-label for="label_en" :value="__('messages.field_label_en')" />
                    <x-text-input id="label_en" name="label_en" type="text" :value="old('label_en', $field->label_en)" />
                    <x-input-error :messages="$errors->get('label_en')" />
                </div>

                <div>
                    <x-input-label for="name_key" :value="__('messages.field_key')" />
                    <x-text-input id="name_key" name="name_key" type="text" dir="ltr" :value="old('name_key', $field->name_key)" required />
                    <x-input-error :messages="$errors->get('name_key')" />
                </div>

                <div>
                    <x-input-label for="placeholder" :value="__('messages.placeholder_ar_optional')" />
                    <x-text-input id="placeholder" name="placeholder" type="text" :value="old('placeholder', $field->placeholder)" />
                </div>

                <div>
                    <x-input-label for="placeholder_en" :value="__('messages.placeholder_en_optional')" />
                    <x-text-input id="placeholder_en" name="placeholder_en" type="text" :value="old('placeholder_en', $field->placeholder_en)" />
                </div>

                <div>
                    <x-input-label for="validation_rules" :value="__('messages.validation_rules_optional')" />
                    <x-text-input id="validation_rules" name="validation_rules" type="text" :value="old('validation_rules', $field->validation_rules)" />
                </div>

                <div>
                    <x-input-label for="additional_rules_en" value="قواعد إضافية إنجليزي" />
                    <textarea id="additional_rules_en" name="additional_rules_en" rows="3"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white"
                        dir="ltr">{{ old('additional_rules_en', $field->additional_rules_en) }}</textarea>
                    <x-input-error :messages="$errors->get('additional_rules_en')" />
                </div>

                <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                    <input id="is_required" name="is_required" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $field->is_required ? 'checked' : '' }}>
                    <label for="is_required">{{ __('messages.required_field') }}</label>
                </div>

                <div>
                    <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $field->sort_order)" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button>{{ __('messages.update') }}</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.services.fields.destroy', [$service, $field]) }}" class="mt-6">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-rose-200 dark:border-rose-800 px-4 py-2 text-sm font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30">{{ __('messages.delete') }}</button>
            </form>
        </div>
    </div>
@endsection
