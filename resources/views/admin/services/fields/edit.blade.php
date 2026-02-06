@extends('layouts.app')

@section('title', 'تعديل حقل')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">تعديل الحقل</h1>
            <a href="{{ route('admin.services.edit', $service) }}" class="text-sm text-emerald-700 dark:text-emerald-400">عودة للخدمة</a>
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
                    <x-input-label for="type" value="نوع الحقل" />
                <select id="type" name="type" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white" required>
                        <option value="text" @selected(old('type', $field->type) === 'text')>نصي</option>
                        <option value="textarea" @selected(old('type', $field->type) === 'textarea')>نص متعدد الأسطر</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" />
                </div>

                <div>
                    <x-input-label for="label" value="عنوان الحقل" />
                    <x-text-input id="label" name="label" type="text" :value="old('label', $field->label)" required />
                    <x-input-error :messages="$errors->get('label')" />
                </div>

                <div>
                    <x-input-label for="name_key" value="مفتاح الحقل" />
                    <x-text-input id="name_key" name="name_key" type="text" :value="old('name_key', $field->name_key)" required />
                    <x-input-error :messages="$errors->get('name_key')" />
                </div>

                <div>
                    <x-input-label for="placeholder" value="نص توضيحي (اختياري)" />
                    <x-text-input id="placeholder" name="placeholder" type="text" :value="old('placeholder', $field->placeholder)" />
                </div>

                <div>
                    <x-input-label for="validation_rules" value="قواعد إضافية (اختياري)" />
                    <x-text-input id="validation_rules" name="validation_rules" type="text" :value="old('validation_rules', $field->validation_rules)" />
                </div>

            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input id="is_required" name="is_required" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $field->is_required ? 'checked' : '' }}>
                    <label for="is_required">حقل مطلوب</label>
                </div>

                <div>
                    <x-input-label for="sort_order" value="ترتيب العرض" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $field->sort_order)" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button>تحديث</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.services.fields.destroy', [$service, $field]) }}" class="mt-6">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-rose-200 dark:border-rose-800 px-4 py-2 text-sm font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30">حذف الحقل</button>
            </form>
        </div>
    </div>
@endsection
