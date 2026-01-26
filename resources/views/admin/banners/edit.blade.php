@extends('layouts.app')

@section('title', 'تعديل بانر')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">تعديل بانر</h1>

        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="title" value="العنوان (اختياري)" />
                <x-text-input id="title" name="title" type="text" :value="old('title', $banner->title)" />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="image" value="الصورة (يمكن استبدالها)" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <x-input-error :messages="$errors->get('image')" />
                <p class="mt-2 text-xs text-slate-500">الصورة الحالية تظهر في السلايدر.</p>
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ $banner->is_active ? 'checked' : '' }}>
                <label for="is_active">تفعيل البانر</label>
            </div>

            <div>
                <x-input-label for="sort_order" value="ترتيب العرض" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $banner->sort_order)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>تحديث</x-primary-button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">عودة</a>
            </div>
        </form>
    </div>
@endsection
