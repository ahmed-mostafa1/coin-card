@extends('layouts.app')

@section('title', 'المظهر العام')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">المظهر العام</h1>
        <p class="mt-2 text-sm text-slate-600">تعديل شريط الملاحظة المتحرك.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.appearance.update') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="ticker_text" value="نص الشريط المتحرك" />
                <textarea id="ticker_text" name="ticker_text" rows="3" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3 text-sm text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('ticker_text', $tickerText) }}</textarea>
                <x-input-error :messages="$errors->get('ticker_text')" />
            </div>

            <x-primary-button>حفظ</x-primary-button>
        </form>
    </div>
@endsection
