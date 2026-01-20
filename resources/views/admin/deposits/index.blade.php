@extends('layouts.app')

@section('title', 'طلبات الشحن')

@section('content')
    <x-card :hover="false">
        <x-page-header title="طلبات الشحن" subtitle="مراجعة طلبات الشحن اليدوية." />

        <form class="mt-6 flex flex-wrap gap-3" method="GET">
            <x-select name="status">
                <option value="">كل الحالات</option>
                <option value="pending" @selected(request('status') === 'pending')>قيد المراجعة</option>
                <option value="approved" @selected(request('status') === 'approved')>مقبول</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
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
                        <th class="py-2">الطريقة</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                        <th class="py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deposits as $deposit)
                        <tr class="transition hover:bg-slate-50">
                            <td class="py-3 text-slate-700">{{ $deposit->user->name }}<div class="text-xs text-slate-500">{{ $deposit->user->email }}</div></td>
                            <td class="py-3 text-slate-700">{{ $deposit->paymentMethod->name }}</td>
                            <td class="py-3 text-slate-700">{{ number_format($deposit->user_amount, 2) }} USD</td>
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
                                <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">لا توجد طلبات.</td>
                        </tr>
                    @endforelse
                </tbody>
        </x-table>

        <div class="mt-6">{{ $deposits->links() }}</div>
    </x-card>
@endsection
