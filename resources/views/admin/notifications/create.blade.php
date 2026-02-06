@extends('layouts.app')

@section('title', 'إرسال إشعار عام')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">إرسال إشعار لجميع المستخدمين</h1>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">سيتم إرسال هذا الإشعار عبر البريد الإلكتروني ويظهر في الموقع لجميع المستخدمين.</p>

            @if (session('status'))
                <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.notifications.store') }}" class="mt-6 space-y-6">
                @csrf
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="title_ar" value="العنوان (عربي)" />
                        <x-text-input id="title_ar" name="title_ar" type="text" class="mt-1 w-full" :value="old('title_ar')" required />
                        <x-input-error :messages="$errors->get('title_ar')" />
                    </div>
                    <div>
                        <x-input-label for="title_en" value="العنوان (إنجليزي)" />
                        <x-text-input id="title_en" name="title_en" type="text" class="mt-1 w-full" :value="old('title_en')" required />
                        <x-input-error :messages="$errors->get('title_en')" />
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="content_ar" value="المحتوى (عربي)" />
                        <textarea id="content_ar" name="content_ar" rows="4" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/50 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 focus:border-emerald-500 dark:focus:border-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600" required>{{ old('content_ar') }}</textarea>
                        <x-input-error :messages="$errors->get('content_ar')" />
                    </div>
                    <div>
                        <x-input-label for="content_en" value="المحتوى (إنجليزي)" />
                        <textarea id="content_en" name="content_en" rows="4" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/50 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 focus:border-emerald-500 dark:focus:border-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-600" required>{{ old('content_en') }}</textarea>
                        <x-input-error :messages="$errors->get('content_en')" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>إرسال للجميع</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
