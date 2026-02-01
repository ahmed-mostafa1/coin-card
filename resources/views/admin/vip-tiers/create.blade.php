@extends('layouts.app')

@section('title', 'إضافة مستوى VIP')
@section('mainWidth', 'max-w-3xl w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">إضافة مستوى VIP جديد</h1>
            <a href="{{ route('admin.vip-tiers.index') }}" class="text-sm text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">
                <i class="fa-solid fa-arrow-right ml-1"></i>
                عودة للقائمة
            </a>
        </div>

        <form method="POST" action="{{ route('admin.vip-tiers.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title AR -->
                <div>
                    <x-input-label for="title_ar" value="الاسم (عربي)" />
                    <x-text-input id="title_ar" name="title_ar" type="text" class="mt-1 block w-full" :value="old('title_ar')" required autofocus />
                    <x-input-error :messages="$errors->get('title_ar')" class="mt-2" />
                </div>

                <!-- Title EN -->
                <div>
                    <x-input-label for="title_en" value="الاسم (إنجليزي)" />
                    <x-text-input id="title_en" name="title_en" type="text" class="mt-1 block w-full" :value="old('title_en')" />
                    <x-input-error :messages="$errors->get('title_en')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rank -->
                <div>
                    <x-input-label for="rank" value="الرتبة (ترتيب الظهور)" />
                    <x-text-input id="rank" name="rank" type="number" class="mt-1 block w-full" :value="old('rank', 1)" required min="0" />
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">كلما قل الرقم، ظهر المستوى أولاً (أو اعتبر أقل).</p>
                    <x-input-error :messages="$errors->get('rank')" class="mt-2" />
                </div>

                <!-- Deposits Required -->
                <div>
                    <x-input-label for="deposits_required" value="مجموع الإيداعات المطلوب ($)" />
                    <x-text-input id="deposits_required" name="deposits_required" type="number" step="0.01" class="mt-1 block w-full" :value="old('deposits_required', 0)" required min="0" />
                    <x-input-error :messages="$errors->get('deposits_required')" class="mt-2" />
                </div>
            </div>

            <!-- Image -->
            <div>
                <x-input-label for="image_path" value="صورة الشعار" />
                <input type="file" id="image_path" name="image_path" accept="image/*" class="mt-1 block w-full rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <x-input-error :messages="$errors->get('image_path')" class="mt-2" />
            </div>

            <!-- Is Active -->
            <div class="block">
                <label for="is_active" class="inline-flex items-center">
                    <input id="is_active" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:focus:ring-emerald-600 dark:focus:ring-offset-slate-800" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">تفعيل هذا المستوى</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>حفظ</x-primary-button>
                <a href="{{ route('admin.vip-tiers.index') }}" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
