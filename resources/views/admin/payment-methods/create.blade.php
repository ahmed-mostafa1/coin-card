@extends('layouts.app')

@section('title', __('messages.add_payment_method'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.add_payment_method') }}</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __('messages.enter_payment_method_details') }}</p>

        <form method="POST" action="{{ route('admin.payment-methods.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('messages.payment_method_name_ar')" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="name_en" :value="__('messages.payment_method_name_en')" />
                <x-text-input id="name_en" name="name_en" type="text" :value="old('name_en')" />
                <x-input-error :messages="$errors->get('name_en')" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('messages.slug')" />
                <x-text-input id="slug" name="slug" type="text" :value="old('slug')" required />
                <x-input-error :messages="$errors->get('slug')" />
            </div>

            <div>
                <x-input-label for="instructions" :value="__('messages.instructions_ar')" />
                <textarea id="instructions" name="instructions" rows="5" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('instructions') }}</textarea>
                <x-input-error :messages="$errors->get('instructions')" />
            </div>

            <div>
                <x-input-label for="instructions_en" :value="__('messages.instructions_en')" />
                <textarea id="instructions_en" name="instructions_en" rows="5" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('instructions_en') }}</textarea>
                <x-input-error :messages="$errors->get('instructions_en')" />
            </div>

            <div>
                <x-input-label for="icon" :value="__('messages.icon_optional')" />
                <input id="icon" name="icon" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                <x-input-error :messages="$errors->get('icon')" />
            </div>

            <div>
                <x-input-label for="account_number" :value="__('messages.account_number')" />
                <x-text-input id="account_number" name="account_number" type="text" :value="old('account_number')" required />
                <x-input-error :messages="$errors->get('account_number')" />
            </div>


            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">{{ __('messages.activate_payment_method') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
                <x-input-error :messages="$errors->get('sort_order')" />
            </div>

            
            @php
                $fields = old('fields', []);
            @endphp

            <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.additional_fields') }}</h2>
                    <button type="button" class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300" data-field-add>{{ __('messages.add_field') }}</button>
                </div>
                <div class="mt-4 space-y-4" data-fields-container>
                    @foreach ($fields as $index => $field)
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4" data-field-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <x-input-label value="{{ __('messages.field_label') }}" />
                                    <x-text-input name="fields[{{ $index }}][label]" type="text" :value="$field['label'] ?? ''" required data-field-label-input />
                                    <x-input-error :messages="$errors->get('fields.'.$index.'.label')" />
                                </div>
                                <div>
                                    <x-input-label value="{{ __('messages.field_key') }}" />
                                    <x-text-input name="fields[{{ $index }}][name_key]" type="text" :value="$field['name_key'] ?? ''" required data-field-key-input />
                                    <x-input-error :messages="$errors->get('fields.'.$index.'.name_key')" />
                                </div>
                                <div>
                                    <x-input-label value="{{ __('messages.sort_order') }}" />
                                    <x-text-input name="fields[{{ $index }}][sort_order]" type="number" min="0" :value="$field['sort_order'] ?? 0" />
                                </div>
                            </div>
                            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                                <div>
                                    <x-input-label value="{{ __('messages.field_type') }}" />
                                    <select name="fields[{{ $index }}][type]" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500" required>
                                        <option value="text" @selected(($field['type'] ?? 'text') === 'text')>{{ __('messages.field_type_text') }}</option>
                                        <option value="textarea" @selected(($field['type'] ?? 'text') === 'textarea')>{{ __('messages.field_type_textarea') }}</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                    <input type="checkbox" name="fields[{{ $index }}][is_required]" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ ($field['is_required'] ?? true) ? 'checked' : '' }}>
                                    {{ __('messages.required_field') }}
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="button" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700" data-field-remove>{{ __('messages.remove') }}</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const fieldsContainer = document.querySelector('[data-fields-container]');
                    const addFieldButton = document.querySelector('[data-field-add]');

                    const slugify = (text) => {
                        return text.toString().toLowerCase()
                            .replace(/\s+/g, '_')           // Replace spaces with _
                            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                            .replace(/\-\-+/g, '_')         // Replace multiple - with single _
                            .replace(/__+/g, '_')          // Replace multiple _ with single _
                            .replace(/^-+/, '')             // Trim - from start of text
                            .replace(/-+$/, '');            // Trim - from end of text
                    };

                    const handleLabelInput = (event) => {
                        const labelInput = event.target;
                        if (!labelInput.matches('[data-field-label-input]')) return;

                        const row = labelInput.closest('[data-field-row]');
                        const keyInput = row.querySelector('[data-field-key-input]');
                        
                        // Only auto-generate if key is empty or looks like a slug of the previous value (simplest check: if it's empty)
                        // Or we can track if user manually edited it. For now, empty check is safest.
                        if (keyInput && keyInput.value.trim() === '') {
                             keyInput.value = slugify(labelInput.value);
                        }
                    };
                    
                    fieldsContainer.addEventListener('input', handleLabelInput);


                    const buildFieldRow = (index) => `
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4" data-field-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.field_label') }}</label>
                                    <input name="fields[${index}][label]" type="text" required data-field-label-input class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.field_key') }}</label>
                                    <input name="fields[${index}][name_key]" type="text" required data-field-key-input class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.sort_order') }}</label>
                                    <input name="fields[${index}][sort_order]" type="number" min="0" value="${index}" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>
                            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.field_type') }}</label>
                                    <select name="fields[${index}][type]" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500" required>
                                        <option value="text">{{ __('messages.field_type_text') }}</option>
                                        <option value="textarea">{{ __('messages.field_type_textarea') }}</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                    <input type="checkbox" name="fields[${index}][is_required]" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" checked>
                                    {{ __('messages.required_field') }}
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="button" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700" data-field-remove>{{ __('messages.remove') }}</button>
                                </div>
                            </div>
                        </div>
                    `;

                    addFieldButton?.addEventListener('click', () => {
                        const index = fieldsContainer.querySelectorAll('[data-field-row]').length;
                        fieldsContainer.insertAdjacentHTML('beforeend', buildFieldRow(index));
                    });

                    document.addEventListener('click', (event) => {
                        const fieldRemove = event.target.closest('[data-field-remove]');
                        if (fieldRemove && fieldsContainer) {
                            fieldRemove.closest('[data-field-row]')?.remove();
                        }
                    });
                });
            </script>


            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
                <a href="{{ route('admin.payment-methods.index') }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-500">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection