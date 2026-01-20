@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">طلب #{{ $order->id }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ $order->service->name }}</p>
                </div>
                <a href="{{ route('account.orders') }}" class="text-sm text-emerald-700 hover:text-emerald-900">عودة للطلبات</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">السعر</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($order->price_at_purchase, 2) }} ر.س</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">الحالة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        @if ($order->status === 'new')
                            جديد
                        @elseif ($order->status === 'processing')
                            قيد التنفيذ
                        @elseif ($order->status === 'done')
                            مكتمل
                        @elseif ($order->status === 'rejected')
                            مرفوض
                        @else
                            ملغي
                        @endif
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">التاريخ</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">بيانات الطلب</p>
                @if (count($order->payload))
                    <div class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach ($order->payload as $key => $value)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">{{ $fieldLabels[$key] ?? $key }}</span>
                                <span class="font-semibold">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">لا توجد بيانات إضافية.</p>
                @endif
            </div>

            @if ($order->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-700">
                    ملاحظة الإدارة: {{ $order->admin_note }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">التحديثات</h2>
            <p class="mt-3 text-sm text-slate-600">سيتم تحديث حالة الطلب بمجرد المعالجة من الإدارة.</p>
        </div>
    </div>
@endsection
