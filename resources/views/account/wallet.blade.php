@extends('layouts.app')

@section('title', 'سجل الرصيد')

@section('content')
    <x-card :hover="false">
        <x-page-header title="سجل الرصيد" subtitle="جميع حركات الرصيد في محفظتك.">
            <x-slot name="actions">
                <a href="{{ route('deposit.index') }}" class="inline-flex items-center justify-center rounded-xl border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 cc-press">شحن رصيد</a>
            </x-slot>
        </x-page-header>

        <x-table class="mt-6">
            <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-2">النوع</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $typeLabels = [
                            'deposit' => 'شحن',
                            'hold' => 'تعليق مبلغ',
                            'settle' => 'تأكيد الخصم',
                            'release' => 'إرجاع الرصيد المعلّق',
                            'purchase' => 'شراء',
                        ];
                    @endphp
                    @forelse ($transactions as $transaction)
                        @php
                            $amount = (float) $transaction->amount;
                            $displayAmount = $amount;

                            if ($amount >= 0 && in_array($transaction->type, ['hold', 'settle', 'purchase'], true)) {
                                $displayAmount = -$amount;
                            }

                            $amountClass = $displayAmount >= 0 ? 'text-emerald-700' : 'text-rose-700';
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="py-3 text-slate-700">
                                {{ $typeLabels[$transaction->type] ?? $transaction->type }}
                            </td>
                            <td class="py-3">
                                <span class="{{ $amountClass }}">
                                    {{ number_format($displayAmount, 2) }} USD
                                </span>
                            </td>
                            <td class="py-3">
                                @if ($transaction->status === 'approved')
                                    <x-badge type="approved">معتمد</x-badge>
                                @elseif ($transaction->status === 'pending')
                                    <x-badge type="pending">قيد المعالجة</x-badge>
                                @else
                                    <x-badge type="rejected">مرفوض</x-badge>
                                @endif
                            </td>
                            <td class="py-3 text-slate-500">{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 text-slate-500">{{ $transaction->note ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500">لا توجد عمليات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
        </x-table>

        <div class="mt-6">{{ $transactions->links() }}</div>
    </x-card>
@endsection
