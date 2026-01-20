@extends('layouts.app')

@section('title', 'حسابي')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <h1 class="text-2xl font-semibold text-emerald-700">حسابي</h1>
            <p class="mt-3 text-sm text-slate-600">متابعة الرصيد وطلبات الشحن وسجل العمليات.</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <a href="{{ route('deposit.index') }}" class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4 text-center text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">شحن الرصيد</a>
                <a href="{{ route('account.deposits') }}" class="rounded-2xl border border-slate-200 px-4 py-4 text-center text-sm font-semibold text-slate-700 transition hover:border-emerald-200">طلبات الشحن</a>
                <a href="{{ route('account.wallet') }}" class="rounded-2xl border border-slate-200 px-4 py-4 text-center text-sm font-semibold text-slate-700 transition hover:border-emerald-200">سجل الرصيد</a>
            </div>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-600 to-emerald-700 p-8 text-white shadow-lg">
            <h2 class="text-lg font-semibold">رصيد المحفظة الحالي</h2>
            <p class="mt-4 text-3xl font-semibold">{{ number_format($wallet->balance, 2) }} ر.س</p>
            <p class="mt-2 text-sm text-emerald-100">آخر تحديث للرصيد يتم عبر موافقة الإدارة.</p>
        </div>
    </div>
@endsection
