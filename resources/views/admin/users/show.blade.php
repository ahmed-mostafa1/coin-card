@extends('layouts.app')

@section('title', 'ملف المستخدم')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">{{ $user->name }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ $user->email }}</p>
                </div>
                <div class="text-sm text-slate-600">
                    <p>تاريخ الإنشاء: {{ $user->created_at->format('Y-m-d') }}</p>
                    <p>الأدوار: {{ $user->roles->pluck('name')->implode('، ') ?: 'بدون دور' }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700">المحفظة</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">الرصيد المتاح</span>
                        <span class="font-semibold text-slate-700">{{ number_format($wallet->balance, 2) }} ر.س</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">الرصيد المعلّق</span>
                        <span class="font-semibold text-slate-700">{{ number_format($wallet->held_balance, 2) }} ر.س</span>
                    </div>
                </div>
            </div>
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="text-lg font-semibold text-emerald-700">آخر حركات الرصيد</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="border-b border-slate-200 text-xs text-slate-500">
                            <tr>
                                <th class="py-2">النوع</th>
                                <th class="py-2">المبلغ</th>
                                <th class="py-2">المرجع</th>
                                <th class="py-2">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="py-3 text-slate-700">
                                        @if ($transaction->type === 'deposit')
                                            شحن
                                        @elseif ($transaction->type === 'hold')
                                            تعليق
                                        @elseif ($transaction->type === 'settle')
                                            تسوية
                                        @elseif ($transaction->type === 'release')
                                            إرجاع
                                        @else
                                            {{ $transaction->type }}
                                        @endif
                                    </td>
                                    <td class="py-3 text-slate-700">{{ number_format($transaction->amount, 2) }} ر.س</td>
                                    <td class="py-3 text-slate-500">
                                        {{ $transaction->reference_type }} #{{ $transaction->reference_id ?? '-' }}
                                    </td>
                                    <td class="py-3 text-slate-500">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-slate-500">لا توجد حركات حتى الآن.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700">آخر طلبات الشحن</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="border-b border-slate-200 text-xs text-slate-500">
                            <tr>
                                <th class="py-2">الحالة</th>
                                <th class="py-2">الطريقة</th>
                                <th class="py-2">المبلغ</th>
                                <th class="py-2">المعتمد</th>
                                <th class="py-2">تاريخ المراجعة</th>
                                <th class="py-2">التاريخ</th>
                                <th class="py-2">عرض</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($deposits as $deposit)
                                <tr>
                                    <td class="py-3">
                                        @if ($deposit->status === 'pending')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">قيد المراجعة</span>
                                        @elseif ($deposit->status === 'approved')
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">معتمد</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-slate-700">{{ $deposit->paymentMethod->name }}</td>
                                    <td class="py-3 text-slate-700">{{ number_format($deposit->user_amount, 2) }} ر.س</td>
                                    <td class="py-3 text-slate-700">
                                        {{ $deposit->approved_amount ? number_format($deposit->approved_amount, 2) : '-' }} ر.س
                                    </td>
                                    <td class="py-3 text-slate-500">{{ $deposit->reviewed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td class="py-3 text-slate-500">{{ $deposit->created_at->format('Y-m-d') }}</td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-slate-500">لا توجد طلبات شحن.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700">آخر الطلبات</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="border-b border-slate-200 text-xs text-slate-500">
                            <tr>
                                <th class="py-2">الحالة</th>
                                <th class="py-2">الخدمة</th>
                                <th class="py-2">الباقة</th>
                                <th class="py-2">المبلغ</th>
                                <th class="py-2">تسوية/إرجاع</th>
                                <th class="py-2">التاريخ</th>
                                <th class="py-2">عرض</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="py-3">
                                        @if ($order->status === 'new')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">جديد</span>
                                        @elseif ($order->status === 'processing')
                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">قيد التنفيذ</span>
                                        @elseif ($order->status === 'done')
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">تم التنفيذ</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                                    <td class="py-3 text-slate-700">{{ $order->variant?->name ?? 'السعر الأساسي' }}</td>
                                    <td class="py-3 text-slate-700">{{ number_format($order->amount_held, 2) }} ر.س</td>
                                    <td class="py-3 text-slate-500">{{ $order->settled_at?->format('Y-m-d H:i') ?? $order->released_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td class="py-3 text-slate-500">{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-slate-500">لا توجد طلبات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
