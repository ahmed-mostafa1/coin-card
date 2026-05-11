@extends('layouts.app')

@section('title', 'إدارة الصفحات')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-800 dark:text-emerald-100">إدارة الصفحات</h1>
                <p class="mt-1 text-sm text-slate-900 dark:text-slate-100">تحكم بمحتوى صفحات من نحن وسياسة الخصوصية وشروط الاستخدام.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-50 dark:bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-900">
                <i class="fa-solid fa-arrow-right ml-2 rtl:ml-0 rtl:mr-2"></i> {{ __('messages.dashboard') ?? 'لوحة التحكم' }}
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pages.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-3xl border border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm" x-data="{ tab: 'about' }">
                <div class="mb-6 flex gap-2 border-b border-slate-50 pb-2 dark:border-slate-700">
                    <button type="button" @click="tab = 'about'" :class="{ 'text-emerald-600 border-b-2 border-emerald-600 font-bold': tab === 'about', 'text-slate-900 hover:text-slate-700': tab !== 'about' }" class="px-4 py-2 text-sm transition">من نحن (About Us)</button>
                    <button type="button" @click="tab = 'privacy'" :class="{ 'text-emerald-600 border-b-2 border-emerald-600 font-bold': tab === 'privacy', 'text-slate-900 hover:text-slate-700': tab !== 'privacy' }" class="px-4 py-2 text-sm transition">سياسة الخصوصية (Privacy)</button>
                    <button type="button" @click="tab = 'terms'" :class="{ 'text-emerald-600 border-b-2 border-emerald-600 font-bold': tab === 'terms', 'text-slate-900 hover:text-slate-700': tab !== 'terms' }" class="px-4 py-2 text-sm transition">شروط الاستخدام (Terms)</button>
                </div>

                <div x-show="tab === 'about'" class="space-y-6">
                    <div>
                        <x-input-label for="about_ar" value="من نحن (عربي)" />
                        <textarea id="about_ar" name="about_ar" rows="15" class="w-full rounded-xl border border-slate-50 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-800 dark:text-slate-50">{{ old('about_ar', $aboutAr ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('about_ar')" />
                    </div>
                    <div>
                        <x-input-label for="about_en" value="من نحن (إنجليزي)" />
                        <textarea id="about_en" name="about_en" rows="15" class="w-full rounded-xl border border-slate-50 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-800 dark:text-slate-50" dir="ltr">{{ old('about_en', $aboutEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('about_en')" />
                    </div>
                </div>

                <div x-show="tab === 'privacy'" class="space-y-6" style="display: none;">
                    <div>
                        <x-input-label for="privacy_ar" value="سياسة الخصوصية (عربي)" />
                        <textarea id="privacy_ar" name="privacy_ar" rows="15" class="w-full rounded-xl border border-slate-50 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-800 dark:text-slate-50">{{ old('privacy_ar', $privacyAr ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('privacy_ar')" />
                    </div>
                    <div>
                        <x-input-label for="privacy_en" value="سياسة الخصوصية (إنجليزي)" />
                        <textarea id="privacy_en" name="privacy_en" rows="15" class="w-full rounded-xl border border-slate-50 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-800 dark:text-slate-50" dir="ltr">{{ old('privacy_en', $privacyEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('privacy_en')" />
                    </div>
                </div>

                <div x-show="tab === 'terms'" class="space-y-6" style="display: none;">
                    <div>
                        <x-input-label for="terms_ar" value="شروط الاستخدام (عربي)" />
                        <textarea id="terms_ar" name="terms_ar" rows="15" class="w-full rounded-xl border border-slate-50 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-800 dark:text-slate-50">{{ old('terms_ar', $termsAr ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('terms_ar')" />
                    </div>
                    <div>
                        <x-input-label for="terms_en" value="شروط الاستخدام (إنجليزي)" />
                        <textarea id="terms_en" name="terms_en" rows="15" class="w-full rounded-xl border border-slate-50 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-800 dark:text-slate-50" dir="ltr">{{ old('terms_en', $termsEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('terms_en')" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <x-primary-button>حفظ المحتوى</x-primary-button>
            </div>
        </form>
    </div>
@endsection
