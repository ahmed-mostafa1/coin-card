@extends('layouts.app')

@section('title', 'طلبات الشحن')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">طلبات الشحن</h1>
                <p class="mt-2 text-sm text-slate-600">مراجعة طلبات الشحن اليدوية.</p>
            </div>
        </div>

        <form class="mt-6 flex flex-wrap gap-3" method="GET">
            <select name="status" class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700">
                <option value="">كل الحالات</option>
                <option value="pending" @selected(request('status') === 'pending')>قيد المراجعة</option>
                <option value="approved" @selected(request('status') === 'approved')>مقبول</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
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
                        <th class="py-2">الطريقة</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deposits as $deposit)
                        <tr>
                            <td class="py-3 text-slate-700">{{ $deposit->user->name }}<div class="text-xs text-slate-500">{{ $deposit->user->email }}</div></td>
                            <td class="py-3 text-slate-700">{{ $deposit->paymentMethod->name }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($deposit->user_amount, 2) }} ر.س</td>
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
                            <td class="py-3">
                                <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
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

        <div class="mt-6">{{ $deposits->links() }}</div>
    </div>
@endsection
