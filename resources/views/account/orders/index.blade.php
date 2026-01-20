@extends('layouts.app')

@section('title', 'طلباتي')

@section('content')
    <x-card :hover="false">
        <x-page-header title="طلباتي" subtitle="استعرض جميع طلبات الخدمات الخاصة بك." />

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="py-2">الخدمة</th>
                    <th class="py-2">الباقة</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">المبلغ المعلّق</th>
                        <th class="py-2">الحالة</th>
                    <th class="py-2">التاريخ</th>
                    <th class="py-2">تفاصيل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    <tr class="transition hover:bg-slate-50">
                        <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                        <td class="py-3 text-slate-700">{{ $order->variant?->name ?? '-' }}</td>
                        <td class="py-3 text-slate-700">{{ number_format($order->price_at_purchase, 2) }} USD</td>
                        <td class="py-3 text-slate-700">{{ number_format($order->amount_held, 2) }} USD</td>
                        <td class="py-3">
                            @if ($order->status === 'new')
                                <x-badge type="new">جديد</x-badge>
                            @elseif ($order->status === 'processing')
                                <x-badge type="processing">قيد التنفيذ</x-badge>
                            @elseif ($order->status === 'done')
                                <x-badge type="done">تم التنفيذ</x-badge>
                            @elseif ($order->status === 'rejected')
                                <x-badge type="rejected">مرفوض</x-badge>
                            @else
                                <x-badge>ملغي</x-badge>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500">{{ $order->created_at->format('Y-m-d') }}</td>
                        <td class="py-3">
                            <a href="{{ route('account.orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-500">لا توجد طلبات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-6">{{ $orders->links() }}</div>
    </x-card>
@endsection
