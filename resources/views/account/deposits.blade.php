@extends('layouts.app')

@section('title', 'طلبات الشحن')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <x-card :hover="false">
        <x-page-header title="طلبات الشحن" subtitle="تابع حالة طلبات الشحن الخاصة بك.">
            <x-slot name="actions">
                <a href="{{ route('deposit.index') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:brightness-105 cc-press">شحن رصيد جديد</a>
            </x-slot>
        </x-page-header>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-2">الطريقة</th>
                        <th class="py-2">المبلغ المطلوب</th>
                        <th class="py-2">المبلغ المعتمد</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deposits as $deposit)
                        <tr class="transition hover:bg-slate-50">
                            <td class="py-3 text-slate-700">{{ $deposit->paymentMethod->name }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($deposit->user_amount, 2) }} USD</td>
                            <td class="py-3 text-slate-700">
                                @if ($deposit->approved_amount)
                                    {{ number_format($deposit->approved_amount, 2) }} USD
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3">
                                @if ($deposit->status === 'pending')
                                    <x-badge type="pending">قيد المراجعة</x-badge>
                                @elseif ($deposit->status === 'approved')
                                    <x-badge type="approved">مقبول</x-badge>
                                @else
                                    <x-badge type="rejected">مرفوض</x-badge>
                                @endif
                            </td>
                            <td class="py-3 text-slate-500">{{ $deposit->created_at->format('Y-m-d') }}</td>
                            <td class="py-3">
                                <a href="{{ route('account.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                            </td>
                        </tr>
                        @if ($deposit->status === 'rejected' && $deposit->admin_note)
                            <tr>
                                <td colspan="6" class="pb-4 text-sm text-rose-600">سبب الرفض: {{ $deposit->admin_note }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">لا توجد طلبات شحن بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
        </x-table>

        <div class="mt-6">{{ $deposits->links() }}</div>
    </x-card>
@endsection
