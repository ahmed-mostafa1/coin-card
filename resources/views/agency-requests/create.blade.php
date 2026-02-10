@extends('layouts.app')

@section('title', 'طلب وكالة')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">طلب وكالة</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">املأ البيانات التالية وسيتم التواصل معك.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('agency-requests.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="contact_number" value="رقم للتواصل" />
                <x-text-input id="contact_number" name="contact_number" type="text" :value="old('contact_number')" required />
                @error('contact_number')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label for="full_name" value="اسمك الثلاثي" />
                <x-text-input id="full_name" name="full_name" type="text" :value="old('full_name')" required />
                @error('full_name')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label for="region" value="المنطقة الموجود فيها" />
                <x-text-input id="region" name="region" type="text" :value="old('region')" required />
                @error('region')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label for="starting_amount" value="المبلغ الذي تستطيع بدأ العمل به ؟" />
                <x-text-input id="starting_amount" name="starting_amount" type="number" step="0.01" min="0" :value="old('starting_amount')" required />
                @error('starting_amount')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <x-primary-button class="w-full">إرسال</x-primary-button>
        </form>
    </div>
@endsection
