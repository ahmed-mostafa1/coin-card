@extends('layouts.app')

@section('title', 'تفاصيل طلب الشحن')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">طلب شحن #{{ $depositRequest->id }}</h1>
                    <p class="mt-2 text-sm text-slate-600">تم الإنشاء في {{ $depositRequest->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <a href="{{ route('account.deposits') }}" class="text-sm text-emerald-700 hover:text-emerald-900">عودة لطلبات الشحن</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">طريقة الدفع</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $depositRequest->paymentMethod->name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">مبلغ المستخدم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($depositRequest->user_amount, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">المبلغ المعتمد</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        {{ $depositRequest->approved_amount ? number_format($depositRequest->approved_amount, 2) : '-' }} USD
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">الحالة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        @if ($depositRequest->status === 'pending')
                            قيد المراجعة
                        @elseif ($depositRequest->status === 'approved')
                            معتمد
                        @else
                            مرفوض
                        @endif
                    </p>
                </div>
            </div>

            @if ($depositRequest->status === 'rejected' && $depositRequest->admin_note)
                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    سبب الرفض: {{ $depositRequest->admin_note }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">ملاحظات</h2>
            <p class="mt-3 text-sm text-slate-600">سيتم تحديث حالة الطلب بعد مراجعة الإدارة.</p>
        </div>
    </div>
@endsection
