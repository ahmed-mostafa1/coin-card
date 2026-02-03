@extends('layouts.app')

@section('title', 'تعديل نافذة منبثقة')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">تعديل نافذة منبثقة</h1>
            <a href="{{ route('admin.popups.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-arrow-left"></i>
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('admin.popups.update', $popup) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="title" value="العنوان (عربي)" />
                <x-text-input id="title" name="title" type="text" :value="old('title', $popup->title)" />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="title_en" value="العنوان (إنجليزي)" />
                <x-text-input id="title_en" name="title_en" type="text" :value="old('title_en', $popup->title_en)" />
                <x-input-error :messages="$errors->get('title_en')" />
            </div>

            <div>
                <x-input-label for="content" value="المحتوى (عربي)" />
                <textarea id="content" name="content" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500">{{ old('content', $popup->content) }}</textarea>
                <x-input-error :messages="$errors->get('content')" />
            </div>

            <div>
                <x-input-label for="content_en" value="المحتوى (إنجليزي)" />
                <textarea id="content_en" name="content_en" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500">{{ old('content_en', $popup->content_en) }}</textarea>
                <x-input-error :messages="$errors->get('content_en')" />
            </div>

            <div>
                <x-input-label for="image_path" value="الصورة (اختياري)" />
                @if($popup->image_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $popup->image_path) }}" alt="Current Image" class="h-20 w-auto rounded-lg object-contain bg-slate-100 dark:bg-slate-700 p-1">
                    </div>
                @endif
                <input id="image_path" name="image_path" type="file" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 dark:file:bg-emerald-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:text-emerald-300">
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-info-circle ml-1"></i>
                    {{ __('messages.recommended_size') }}: 600x400 px
                </p>
                <x-input-error :messages="$errors->get('image_path')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $popup->is_active ? 'checked' : '' }}>
                <label for="is_active">تفعيل النافذة</label>
            </div>

            <div>
                <x-input-label for="display_order" value="ترتيب العرض" />
                <x-text-input id="display_order" name="display_order" type="number" min="0" :value="old('display_order', $popup->display_order)" />
            </div>

            <div class="flex gap-3 pt-4">
                <x-primary-button>تحديث</x-primary-button>
                <a href="{{ route('admin.popups.index') }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-500">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
