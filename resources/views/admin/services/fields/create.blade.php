@extends('layouts.app')

@section('title', 'إضافة حقل')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700">إضافة حقل للخدمة</h1>
            <a href="{{ route('admin.services.edit', $service) }}" class="text-sm text-emerald-700">عودة للخدمة</a>
        </div>

        <form method="POST" action="{{ route('admin.services.fields.store', $service) }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="type" value="نوع الحقل" />
                <select id="type" name="type" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                    <option value="text" @selected(old('type') === 'text')>نصي</option>
                    <option value="select" @selected(old('type') === 'select')>قائمة اختيار</option>
                </select>
                <x-input-error :messages="$errors->get('type')" />
            </div>

            <div>
                <x-input-label for="label" value="عنوان الحقل" />
                <x-text-input id="label" name="label" type="text" :value="old('label')" required />
                <x-input-error :messages="$errors->get('label')" />
            </div>

            <div>
                <x-input-label for="name_key" value="مفتاح الحقل" />
                <x-text-input id="name_key" name="name_key" type="text" :value="old('name_key')" required />
                <x-input-error :messages="$errors->get('name_key')" />
            </div>

            <div>
                <x-input-label for="placeholder" value="نص توضيحي (اختياري)" />
                <x-text-input id="placeholder" name="placeholder" type="text" :value="old('placeholder')" />
            </div>

            <div>
                <x-input-label for="validation_rules" value="قواعد إضافية (اختياري)" />
                <x-text-input id="validation_rules" name="validation_rules" type="text" :value="old('validation_rules')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_required" name="is_required" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_required">حقل مطلوب</label>
            </div>

            <div>
                <x-input-label for="sort_order" value="ترتيب العرض" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>حفظ</x-primary-button>
            </div>
        </form>
    </div>
@endsection
