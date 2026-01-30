@extends('layouts.app')

@section('title', __('messages.add_service'))

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.add_service') }}</h1>

        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="category_id" :value="__('messages.category')" />
                <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500" required>
                    <option value="">{{ __('messages.select_category') }}</option>
                    @foreach ($categories as $category)
                        @php
                            $label = $category->parent ? $category->parent->name . ' › ' . $category->name : $category->name;
                        @endphp
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" />
            </div>

            <div>
                <x-input-label for="name" :value="__('messages.service_name_ar')" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="name_en" :value="__('messages.service_name_en')" />
                <x-text-input id="name_en" name="name_en" type="text" :value="old('name_en')" />
                <x-input-error :messages="$errors->get('name_en')" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('messages.slug_optional')" />
                <x-text-input id="slug" name="slug" type="text" :value="old('slug')" />
                <x-input-error :messages="$errors->get('slug')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('messages.description_ar')" />
                <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="description_en" :value="__('messages.description_en')" />
                <textarea id="description_en" name="description_en" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">{{ old('description_en') }}</textarea>
                <x-input-error :messages="$errors->get('description_en')" />
            </div>

            <div>
                <x-input-label for="price" :value="__('messages.price')" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="1" :value="old('price')" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>

            <div>
                <x-input-label for="image" :value="__('messages.image_optional')" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                <x-input-error :messages="$errors->get('image')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">{{ __('messages.activate_service') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            @php
                $variants = old('variants', []);
                $fields = old('fields', []);
            @endphp

            <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.variants') }}</h2>
                    <button type="button" class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300" data-variant-add>{{ __('messages.add_variant') }}</button>
                </div>
                <div class="mt-4 space-y-4" data-variants-container>
                    @foreach ($variants as $index => $variant)
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4" data-variant-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <x-input-label value="{{ __('messages.variant_name') }}" />
                                    <x-text-input name="variants[{{ $index }}][name]" type="text" :value="$variant['name'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('variants.'.$index.'.name')" />
                                </div>
                                <div>
                                    <x-input-label value="{{ __('messages.price') }}" />
                                    <x-text-input name="variants[{{ $index }}][price]" type="number" step="0.01" min="0.01" :value="$variant['price'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('variants.'.$index.'.price')" />
                                </div>
                                <div>
                                    <x-input-label value="{{ __('messages.sort_order') }}" />
                                    <x-text-input name="variants[{{ $index }}][sort_order]" type="number" min="0" :value="$variant['sort_order'] ?? 0" />
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600 dark:text-slate-400">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ ($variant['is_active'] ?? true) ? 'checked' : '' }}>
                                    {{ __('messages.activate_variant') }}
                                </label>
                                <button type="button" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700" data-variant-remove>{{ __('messages.remove') }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.form_fields') }}</h2>
                    <button type="button" class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300" data-field-add>{{ __('messages.add_field') }}</button>
                </div>
                <div class="mt-4 space-y-4" data-fields-container>
                    @foreach ($fields as $index => $field)
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4" data-field-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <x-input-label value="{{ __('messages.field_label') }}" />
                                    <x-text-input name="fields[{{ $index }}][label]" type="text" :value="$field['label'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('fields.'.$index.'.label')" />
                                </div>
                                <div>
                                    <x-input-label value="{{ __('messages.field_key') }}" />
                                    <x-text-input name="fields[{{ $index }}][name_key]" type="text" :value="$field['name_key'] ?? ''" required />
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
                    const variantsContainer = document.querySelector('[data-variants-container]');
                    const fieldsContainer = document.querySelector('[data-fields-container]');

                    const addVariantButton = document.querySelector('[data-variant-add]');
                    const addFieldButton = document.querySelector('[data-field-add]');

                    const buildVariantRow = (index) => `
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4" data-variant-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.variant_name') }}</label>
                                    <input name="variants[${index}][name]" type="text" required class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.price') }}</label>
                                    <input name="variants[${index}][price]" type="number" step="0.01" min="0.01" required class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.sort_order') }}</label>
                                    <input name="variants[${index}][sort_order]" type="number" min="0" value="${index}" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600 dark:text-slate-400">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="variants[${index}][is_active]" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" checked>
                                    {{ __('messages.activate_variant') }}
                                </label>
                                <button type="button" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700" data-variant-remove>{{ __('messages.remove') }}</button>
                            </div>
                        </div>
                    `;

                    const buildFieldRow = (index) => `
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4" data-field-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.field_label') }}</label>
                                    <input name="fields[${index}][label]" type="text" required class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('messages.field_key') }}</label>
                                    <input name="fields[${index}][name_key]" type="text" required class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500">
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

                    const refreshRemoveButtons = () => {
                        const variantRows = variantsContainer.querySelectorAll('[data-variant-row]');
                        variantRows.forEach((row) => {
                            const button = row.querySelector('[data-variant-remove]');
                            if (button) {
                                button.disabled = false;
                            }
                        });

                        const fieldRows = fieldsContainer.querySelectorAll('[data-field-row]');
                        fieldRows.forEach((row) => {
                            const button = row.querySelector('[data-field-remove]');
                            if (button) {
                                button.disabled = false;
                            }
                        });
                    };

                    addVariantButton?.addEventListener('click', () => {
                        const index = variantsContainer.querySelectorAll('[data-variant-row]').length;
                        variantsContainer.insertAdjacentHTML('beforeend', buildVariantRow(index));
                        refreshRemoveButtons();
                    });

                    addFieldButton?.addEventListener('click', () => {
                        const index = fieldsContainer.querySelectorAll('[data-field-row]').length;
                        fieldsContainer.insertAdjacentHTML('beforeend', buildFieldRow(index));
                        refreshRemoveButtons();
                    });

                    document.addEventListener('click', (event) => {
                        const variantRemove = event.target.closest('[data-variant-remove]');
                        if (variantRemove && variantsContainer) {
                            variantRemove.closest('[data-variant-row]')?.remove();
                            refreshRemoveButtons();
                        }

                        const fieldRemove = event.target.closest('[data-field-remove]');
                        if (fieldRemove && fieldsContainer) {
                            fieldRemove.closest('[data-field-row]')?.remove();
                            refreshRemoveButtons();
                        }
                    });

                    refreshRemoveButtons();
                });
            </script>


            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
                <a href="{{ route('admin.services.index') }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-500">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
