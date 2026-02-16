@extends('layouts.app')

@section('title', 'لوحة الأدمن')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">لوحة الأدمن</h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">إدارة طرق الدفع والطلبات والواجهة.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.ops.index', ['tab' => 'orders_new']) }}" class="cc-pill">لوحة العمليات</a>
            <a href="{{ route('admin.reports.index') }}" class="cc-pill">التقارير</a>
            <a href="{{ route('admin.agency-requests.index') }}" class="cc-pill">طلبات الوكالة</a>
            <a href="{{ route('admin.agency-request-fields.index') }}" class="cc-pill">إدارة صفحة طلب الوكالة</a>
            <a href="{{ route('admin.payment-methods.index') }}" class="cc-pill">طرق الدفع</a>
            <a href="{{ route('admin.deposits.index') }}" class="cc-pill">طلبات الشحن</a>
            <a href="{{ route('admin.users.index') }}" class="cc-pill">المستخدمون</a>
            <a href="{{ route('admin.categories.index') }}" class="cc-pill">التصنيفات</a>
            <a href="{{ route('admin.services.index') }}" class="cc-pill">الخدمات</a>
            <a href="{{ route('admin.orders.index') }}" class="cc-pill">الطلبات</a>
            <a href="{{ route('admin.vip-tiers.index') }}" class="cc-pill">مستويات VIP</a>
            <a href="{{ route('admin.banners.index') }}" class="cc-pill">البانرات</a>
            <a href="{{ route('admin.popups.index') }}" class="cc-pill">النوافذ المنبثقة</a>
            <a href="{{ route('admin.pages.edit') }}" class="cc-pill">محتوى الصفحات</a>
            <a href="{{ route('admin.site-settings.edit') }}" class="cc-pill">إدارة الموقع</a>
        </div>
    </div>
@endsection
