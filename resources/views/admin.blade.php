@extends('layouts.app')

@section('title', 'لوحة الأدمن')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">لوحة الأدمن</h1>
        <p class="mt-3 text-sm text-slate-600">إدارة طرق الدفع وطلبات الشحن.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.ops.index') }}" class="cc-pill">لوحة العمليات</a>
            <a href="{{ route('admin.reports.index') }}" class="cc-pill">التقارير</a>
            <a href="{{ route('admin.agency-requests.index') }}" class="cc-pill">طلبات الوكالة</a>
            <a href="{{ route('admin.payment-methods.index') }}" class="cc-pill">طرق الدفع</a>
            <a href="{{ route('admin.deposits.index') }}" class="cc-pill">طلبات الشحن</a>
            <a href="{{ route('admin.categories.index') }}" class="cc-pill">التصنيفات</a>
            <a href="{{ route('admin.services.index') }}" class="cc-pill">الخدمات</a>
            <a href="{{ route('admin.orders.index') }}" class="cc-pill">الطلبات</a>
        </div>
    </div>
@endsection
