@extends('layouts.app')

@section('title', 'إضافة خدمة')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">إضافة خدمة</h1>

        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="category_id" value="التصنيف" />
                <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                    <option value="">اختر التصنيف</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" />
            </div>

            <div>
                <x-input-label for="name" value="اسم الخدمة" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="slug" value="المعرف المختصر (اختياري)" />
                <x-text-input id="slug" name="slug" type="text" :value="old('slug')" />
                <x-input-error :messages="$errors->get('slug')" />
            </div>

            <div>
                <x-input-label for="description" value="الوصف" />
                <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="price" value="السعر" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="1" :value="old('price')" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>

            <div>
                <x-input-label for="image" value="صورة (اختياري)" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <x-input-error :messages="$errors->get('image')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">تفعيل الخدمة</label>
            </div>

            <div>
                <x-input-label for="sort_order" value="ترتيب العرض" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            @php
                $variants = old('variants', []);
                $fields = old('fields', []);
            @endphp

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700">الباقات</h2>
                    <button type="button" class="text-sm font-semibold text-emerald-700" data-variant-add>إضافة باقة</button>
                </div>
                <div class="mt-4 space-y-4" data-variants-container>
                    @foreach ($variants as $index => $variant)
                        <div class="rounded-2xl border border-slate-200 p-4" data-variant-row>
                            <div class="grid gap-4 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <x-input-label value="اسم الباقة" />
                                    <x-text-input name="variants[{{ $index }}][name]" type="text" :value="$variant['name'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('variants.'.$index.'.name')" />
                                </div>
                                <div>
                                    <x-input-label value="السعر" />
                                    <x-text-input name="variants[{{ $index }}][price]" type="number" step="0.01" min="0.01" :value="$variant['price'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('variants.'.$index.'.price')" />
                                </div>
                                <div>
                                    <x-input-label value="ترتيب العرض" />
                                    <x-text-input name="variants[{{ $index }}][sort_order]" type="number" min="0" :value="$variant['sort_order'] ?? 0" />
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ ($variant['is_active'] ?? true) ? 'checked' : '' }}>
                                    تفعيل الباقة
                                </label>
                                <button type="button" class="text-xs text-rose-600" data-variant-remove>حذف</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700">حقول النموذج</h2>
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
                    const variantsContainer = document.querySelector('[data-variants-container]');
                    const fieldsContainer = document.querySelector('[data-fields-container]');

                    const addVariantButton = document.querySelector('[data-variant-add]');
                    const addFieldButton = document.querySelector('[data-field-add]');

                    const buildVariantRow = (index) => `
                        <div class=\"rounded-2xl border border-slate-200 p-4\" data-variant-row>
                            <div class=\"grid gap-4 lg:grid-cols-4\">
                                <div class=\"lg:col-span-2\">
                                    <label class=\"text-sm font-semibold text-slate-700\">اسم الباقة</label>
                                    <input name=\"variants[${index}][name]\" type=\"text\" required class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\">
                                </div>
                                <div>
                                    <label class=\"text-sm font-semibold text-slate-700\">السعر</label>
                                    <input name=\"variants[${index}][price]\" type=\"number\" step=\"0.01\" min=\"0.01\" required class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\">
                                </div>
                                <div>
                                    <label class=\"text-sm font-semibold text-slate-700\">ترتيب العرض</label>
                                    <input name=\"variants[${index}][sort_order]\" type=\"number\" min=\"0\" value=\"${index}\" class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\">
                                </div>
                            </div>
                            <div class=\"mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600\">
                                <label class=\"flex items-center gap-2\">
                                    <input type=\"checkbox\" name=\"variants[${index}][is_active]\" value=\"1\" class=\"rounded border-slate-300 text-emerald-600 focus:ring-emerald-500\" checked>
                                    تفعيل الباقة
                                </label>
                                <button type=\"button\" class=\"text-xs text-rose-600\" data-variant-remove>حذف</button>
                            </div>
                        </div>
                    `;

                    const buildFieldRow = (index) => `
                        <div class=\"rounded-2xl border border-slate-200 p-4\" data-field-row>
                            <div class=\"grid gap-4 lg:grid-cols-4\">
                                <div class=\"lg:col-span-2\">
                                    <label class=\"text-sm font-semibold text-slate-700\">عنوان الحقل</label>
                                    <input name=\"fields[${index}][label]\" type=\"text\" required class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\">
                                </div>
                                <div>
                                    <label class=\"text-sm font-semibold text-slate-700\">مفتاح الحقل</label>
                                    <input name=\"fields[${index}][name_key]\" type=\"text\" required class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\">
                                </div>
                                <div>
                                    <label class=\"text-sm font-semibold text-slate-700\">ترتيب العرض</label>
                                    <input name=\"fields[${index}][sort_order]\" type=\"number\" min=\"0\" value=\"${index}\" class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\">
                                </div>
                            </div>
                            <div class=\"mt-3 grid gap-3 lg:grid-cols-3\">
                                <div>
                                    <label class=\"text-sm font-semibold text-slate-700\">النوع</label>
                                    <select name=\"fields[${index}][type]\" class=\"mt-2 w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700\" required>
                                        <option value=\"text\">نصي</option>
                                        <option value=\"textarea\">نص متعدد الأسطر</option>
                                    </select>
                                </div>
                                <div class=\"flex items-center gap-2 text-sm text-slate-600\">
                                    <input type=\"checkbox\" name=\"fields[${index}][is_required]\" value=\"1\" class=\"rounded border-slate-300 text-emerald-600 focus:ring-emerald-500\" checked>
                                    حقل مطلوب
                                </div>
                                <div class=\"flex items-center justify-end\">
                                    <button type=\"button\" class=\"text-xs text-rose-600\" data-field-remove>حذف</button>
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
                <x-primary-button>حفظ</x-primary-button>
                <a href="{{ route('admin.services.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
