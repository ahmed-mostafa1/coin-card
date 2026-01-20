@extends('layouts.app')

@section('title', 'الطلبات')

@section('content')
    <x-card :hover="false">
        <x-page-header title="الطلبات" subtitle="متابعة طلبات الخدمات." />

        <form class="mt-6 flex flex-wrap gap-3" method="GET">
            <x-select name="status">
                <option value="">كل الحالات</option>
                <option value="new" @selected(request('status') === 'new')>جديد</option>
                <option value="processing" @selected(request('status') === 'processing')>قيد التنفيذ</option>
                <option value="done" @selected(request('status') === 'done')>تم التنفيذ</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
            </x-select>
            <x-text-input name="q" value="{{ request('q') }}" placeholder="بحث بالبريد أو الاسم" />
            <x-button type="submit">تصفية</x-button>
        </form>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-2">المستخدم</th>
                        <th class="py-2">الخدمة</th>
                        <th class="py-2">السعر</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="transition hover:bg-slate-50">
                            <td class="py-3 text-slate-700">{{ $order->user->name }}<div class="text-xs text-slate-500">{{ $order->user->email }}</div></td>
                            <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($order->price_at_purchase, 2) }} USD</td>
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
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">لا توجد طلبات.</td>
                        </tr>
                    @endforelse
                </tbody>
        </x-table>

        <div class="mt-6">{{ $orders->links() }}</div>
    </x-card>
@endsection
