@extends('layouts.app')

@section('title', 'سجل الرصيد')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">سجل الرصيد</h1>
                <p class="mt-2 text-sm text-slate-600">جميع حركات الرصيد في محفظتك.</p>
            </div>
            <a href="{{ route('deposit.index') }}" class="rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">شحن رصيد</a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="py-2">النوع</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="py-3 text-slate-700">
                                {{ $transaction->type === 'deposit' ? 'شحن رصيد' : $transaction->type }}
                            </td>
                            <td class="py-3">
                                <span class="{{ $transaction->amount >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ number_format($transaction->amount, 2) }} ر.س
                                </span>
                            </td>
                            <td class="py-3">
                                @if ($transaction->status === 'approved')
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">معتمد</span>
                                @elseif ($transaction->status === 'pending')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">قيد المعالجة</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
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
            </table>
        </div>

        <div class="mt-6">{{ $transactions->links() }}</div>
    </div>
@endsection
