@extends('layouts.app')

@section('title', 'الطلبات')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">الطلبات</h1>
                <p class="mt-2 text-sm text-slate-600">متابعة طلبات الخدمات.</p>
            </div>
        </div>

        <form class="mt-6 flex flex-wrap gap-3" method="GET">
            <select name="status" class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700">
                <option value="">كل الحالات</option>
                <option value="new" @selected(request('status') === 'new')>جديد</option>
                <option value="processing" @selected(request('status') === 'processing')>قيد التنفيذ</option>
                <option value="done" @selected(request('status') === 'done')>مكتمل</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="بحث بالبريد أو الاسم" class="rounded-full border border-slate-200 px-4 py-2 text-sm">
            <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">تصفية</button>
        </form>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="border-b border-slate-200 text-slate-500">
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
                        <tr>
                            <td class="py-3 text-slate-700">{{ $order->user->name }}<div class="text-xs text-slate-500">{{ $order->user->email }}</div></td>
                            <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($order->price_at_purchase, 2) }} ر.س</td>
                            <td class="py-3">
                                @if ($order->status === 'new')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">جديد</span>
                                @elseif ($order->status === 'processing')
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">قيد التنفيذ</span>
                                @elseif ($order->status === 'done')
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">مكتمل</span>
                                @elseif ($order->status === 'rejected')
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700">ملغي</span>
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
            </table>
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
@endsection
