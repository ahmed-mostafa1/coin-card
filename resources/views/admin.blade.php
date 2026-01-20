@extends('layouts.app')

@section('title', 'لوحة الأدمن')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">لوحة الأدمن</h1>
        <p class="mt-3 text-sm text-slate-600">إدارة طرق الدفع وطلبات الشحن.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.ops.index') }}" class="rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">لوحة العمليات</a>
            <a href="{{ route('admin.payment-methods.index') }}" class="rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">طرق الدفع</a>
            <a href="{{ route('admin.deposits.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200">طلبات الشحن</a>
            <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200">التصنيفات</a>
            <a href="{{ route('admin.services.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200">الخدمات</a>
            <a href="{{ route('admin.orders.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200">الطلبات</a>
        </div>
    </div>
@endsection
