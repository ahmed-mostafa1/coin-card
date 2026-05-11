@extends('layouts.app')

@section('title', 'الشريط المتحرك')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm dark:border-slate-700 dark:bg-slate-800 transition-colors duration-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-100">الشريط المتحرك</h1>
                <p class="mt-2 text-sm text-slate-900 dark:text-slate-50">تعديل شريط الملاحظة المتحرك.</p>
            </div>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('messages.return_to_dashboard') }}
            </a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:bg-emerald-800 dark:border-emerald-700 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.appearance.update') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="ticker_text" value="نص الشريط المتحرك" />
                <textarea id="ticker_text" name="ticker_text" rows="3" class="w-full rounded-xl border border-slate-50 bg-white/80 px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-slate-700 dark:text-emerald-50" required>{{ old('ticker_text', $tickerText) }}</textarea>
                <x-input-error :messages="$errors->get('ticker_text')" />
            </div>

            <x-primary-button>حفظ</x-primary-button>
        </form>
    </div>
@endsection
