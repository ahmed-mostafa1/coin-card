@extends('layouts.app')

@section('title', 'مستويات التوثيق والولاء')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="مستويات التوثيق والولاء" subtitle="النظام الجديد يعتمد على توثيق الحساب ونقاط الشراء داخل المتجر." />

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500">المستوى الحالي</p>
                <p class="mt-2 text-xl font-bold text-emerald-900">{{ $summary['level_label'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500">النقاط الحالية</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($summary['points']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500">الخصم الحالي</p>
                <p class="mt-2 text-2xl font-bold text-emerald-900">{{ number_format($summary['discount_percentage'], 2) }}%</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500">حالة التوثيق</p>
                <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ auth()->user()->is_verified ? 'موثق' : 'غير موثق' }} <x-user-badge :user="auth()->user()" /></p>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-700 dark:text-slate-50">التقدم للخطوة التالية</span>
                <span class="text-slate-500">{{ number_format($summary['progress_percent'], 0) }}%</span>
            </div>
            <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                <div class="h-full bg-emerald-500" style="width: {{ min(100, max(0, $summary['progress_percent'])) }}%"></div>
            </div>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $summary['next_requirement'] }}</p>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 p-5 dark:border-slate-700">
                <h2 class="font-bold text-slate-900 dark:text-white">المستوى 1</h2>
                <p class="mt-2 text-sm text-slate-900 dark:text-slate-50 dark:text-slate-300">مستخدم غير موثق، بدون خصم.</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-900/20">
                <h2 class="font-bold text-emerald-800 dark:text-emerald-200">المستوى 2</h2>
                <p class="mt-2 text-sm text-emerald-800 dark:text-emerald-200">مستخدم موثق مع خصم يدوي تحدده الإدارة بعد الاعتماد.</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900 dark:bg-amber-900/20">
                <h2 class="font-bold text-amber-800 dark:text-amber-200">المستوى 3</h2>
                <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">مستخدم موثق يستفيد من نقاط الولاء والخصومات التدريجية حسب إعدادات الإدارة.</p>
            </div>
        </div>
    </x-card>
@endsection
