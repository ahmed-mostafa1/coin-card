@extends('layouts.app')

@section('title', __('messages.add_payment_method'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">{{ __('messages.add_payment_method') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('messages.enter_payment_method_details') }}</p>

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
                <textarea id="instructions" name="instructions" rows="5" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('instructions') }}</textarea>
                <x-input-error :messages="$errors->get('instructions')" />
            </div>

            <div>
                <x-input-label for="instructions_en" :value="__('messages.instructions_en')" />
                <textarea id="instructions_en" name="instructions_en" rows="5" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('instructions_en') }}</textarea>
                <x-input-error :messages="$errors->get('instructions_en')" />
            </div>

            <div>
                <x-input-label for="icon" :value="__('messages.icon_optional')" />
                <input id="icon" name="icon" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <x-input-error :messages="$errors->get('icon')" />
            </div>

            <div>
                <x-input-label for="account_number" :value="__('messages.account_number')" />
                <x-text-input id="account_number" name="account_number" type="text" :value="old('account_number')" required />
                <x-input-error :messages="$errors->get('account_number')" />
            </div>


            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
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

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700">حقول إضافية</h2>
                    <button type="button" class="text-sm font-semibold text-emerald-700" data-field-add>إضافة حقل</button>
                </div>
                <div class="mt-4 space-y-4" data-fields-container>
                    @foreach ($fields as $index => $field)
                        <div class="rounded-2xl border border-slate-200 p-4" data-field-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <x-input-label value="عنوان الحقل" />
                                    <x-text-input name="fields[{{ $index }}][label]" type="text" :value="$field['label'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('fields.'.$index.'.label')" />
                                </div>
                                <div>
                                    <x-input-label value="مفتاح الحقل" />
                                    <x-text-input name="fields[{{ $index }}][name_key]" type="text" :value="$field['name_key'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('fields.'.$index.'.name_key')" />
                                </div>
                                <div>
                                    <x-input-label value="ترتيب العرض" />
                                    <x-text-input name="fields[{{ $index }}][sort_order]" type="number" min="0" :value="$field['sort_order'] ?? 0" />
                                </div>
                            </div>
                            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                                <div>
                                    <x-input-label value="النوع" />
                                    <select name="fields[{{ $index }}][type]" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                                        <option value="text" @selected(($field['type'] ?? 'text') === 'text')>نصي</option>
                                        <option value="textarea" @selected(($field['type'] ?? 'text') === 'textarea')>نص متعدد الأسطر</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="fields[{{ $index }}][is_required]" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ ($field['is_required'] ?? true) ? 'checked' : '' }}>
                                    حقل مطلوب
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="button" class="text-xs text-rose-600" data-field-remove>حذف</button>
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

                    const buildFieldRow = (index) => `
                        <div class="rounded-2xl border border-slate-200 p-4" data-field-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <label class="text-sm font-semibold text-slate-700">عنوان الحقل</label>
                                    <input name="fields[${index}][label]" type="text" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">مفتاح الحقل</label>
                                    <input name="fields[${index}][name_key]" type="text" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">ترتيب العرض</label>
                                    <input name="fields[${index}][sort_order]" type="number" min="0" value="${index}" class="mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">
                                </div>
                            </div>
                            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">النوع</label>
                                    <select name="fields[${index}][type]" class="mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                                        <option value="text">نصي</option>
                                        <option value="textarea">نص متعدد الأسطر</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="fields[${index}][is_required]" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                                    حقل مطلوب
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="button" class="text-xs text-rose-600" data-field-remove>حذف</button>
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
                <a href="{{ route('admin.payment-methods.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection