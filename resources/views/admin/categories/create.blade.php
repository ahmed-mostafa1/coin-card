@extends('layouts.app')

@section('title', 'إضافة تصنيف')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">إضافة تصنيف</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="name" value="اسم التصنيف" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="slug" value="المعرّف المختصر (اختياري)" />
                <x-text-input id="slug" name="slug" type="text" :value="old('slug')" />
                <x-input-error :messages="$errors->get('slug')" />
            </div>

            <div>
                <x-input-label for="parent_id" value="التصنيف الأب (اختياري)" />
                <select id="parent_id" name="parent_id"
                        class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">{{ __('messages.root_category_option') }}</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('parent_id')" />
            </div>

            <div>
                <x-input-label for="image" value="صورة (اختياري)" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <x-input-error :messages="$errors->get('image')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">تفعيل التصنيف</label>
            </div>

            <div>
                <x-input-label for="sort_order" value="ترتيب العرض" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>حفظ</x-primary-button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
