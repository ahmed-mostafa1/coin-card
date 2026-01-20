@extends('layouts.app')

@section('title', 'طلبات الشحن')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">طلبات الشحن</h1>
                <p class="mt-2 text-sm text-slate-600">تابع حالة طلبات الشحن الخاصة بك.</p>
            </div>
            <a href="{{ route('deposit.index') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">شحن رصيد جديد</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="py-2">الطريقة</th>
                        <th class="py-2">المبلغ المطلوب</th>
                        <th class="py-2">المبلغ المعتمد</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deposits as $deposit)
                        <tr>
                            <td class="py-3 text-slate-700">{{ $deposit->paymentMethod->name }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($deposit->user_amount, 2) }} ر.س</td>
                            <td class="py-3 text-slate-700">
                                @if ($deposit->approved_amount)
                                    {{ number_format($deposit->approved_amount, 2) }} ر.س
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3">
                                @if ($deposit->status === 'pending')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">قيد المراجعة</span>
                                @elseif ($deposit->status === 'approved')
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">مقبول</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
                                @endif
                            </td>
                            <td class="py-3 text-slate-500">{{ $deposit->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @if ($deposit->status === 'rejected' && $deposit->admin_note)
                            <tr>
                                <td colspan="5" class="pb-4 text-sm text-rose-600">سبب الرفض: {{ $deposit->admin_note }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500">لا توجد طلبات شحن بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $deposits->links() }}</div>
    </div>
@endsection
