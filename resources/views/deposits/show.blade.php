@extends('layouts.app')

@section('title', 'تفاصيل طريقة الشحن')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center gap-4">
                @if ($paymentMethod->icon_path)
                    <img src="{{ asset('storage/'.$paymentMethod->icon_path) }}" alt="{{ $paymentMethod->name }}" class="h-14 w-14 rounded-xl object-cover">
                @else
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        {{ mb_substr($paymentMethod->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">{{ $paymentMethod->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">اتبع التعليمات ثم أرسل إثبات التحويل.</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm text-slate-700 whitespace-pre-line">
                {{ $paymentMethod->instructions }}
            </div>
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">إرسال طلب الشحن</h2>

            <form method="POST" action="{{ route('deposit.store', $paymentMethod->slug) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="amount" value="المبلغ المحول" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01" min="{{ config('coin-card.deposit_min_amount') }}" max="{{ config('coin-card.deposit_max_amount') }}" :value="old('amount')" required />
                    <x-input-error :messages="$errors->get('amount')" />
                </div>

                <div>
                    <x-input-label for="proof" value="إثبات التحويل" />
                    <input id="proof" name="proof" type="file" required class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                    <x-input-error :messages="$errors->get('proof')" />
                </div>

                <x-primary-button class="w-full">تأكيد طلب الشحن</x-primary-button>
            </form>
        </div>
    </div>
@endsection
