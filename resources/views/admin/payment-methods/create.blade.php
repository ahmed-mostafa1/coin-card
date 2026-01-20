@extends('layouts.app')

@section('title', 'إضافة طريقة دفع')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">إضافة طريقة دفع</h1>
        <p class="mt-2 text-sm text-slate-600">أدخل بيانات الطريقة والتعليمات.</p>

        <form method="POST" action="{{ route('admin.payment-methods.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="name" value="اسم الطريقة" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="slug" value="المعرف المختصر" />
                <x-text-input id="slug" name="slug" type="text" :value="old('slug')" required />
                <x-input-error :messages="$errors->get('slug')" />
            </div>

            <div>
                <x-input-label for="instructions" value="تعليمات التحويل" />
                <textarea id="instructions" name="instructions" rows="5" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('instructions') }}</textarea>
                <x-input-error :messages="$errors->get('instructions')" />
            </div>

            <div>
                <x-input-label for="icon" value="أيقونة (اختياري)" />
                <input id="icon" name="icon" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <x-input-error :messages="$errors->get('icon')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">تفعيل الطريقة</label>
            </div>

            <div>
                <x-input-label for="sort_order" value="ترتيب العرض" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
                <x-input-error :messages="$errors->get('sort_order')" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>حفظ</x-primary-button>
                <a href="{{ route('admin.payment-methods.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
