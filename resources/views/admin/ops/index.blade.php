@extends('layouts.app')

@section('title', 'لوحة العمليات')

@section('content')
    <div class="space-y-6">
        <x-card :hover="false">
            <x-page-header title="لوحة العمليات" subtitle="إدارة سريعة للطلبات وطلبات الشحن." />

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">طلبات الشحن المعلقة</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700">{{ $pendingDepositsCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">طلبات جديدة</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700">{{ $newOrdersCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">طلبات قيد التنفيذ</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700">{{ $processingOrdersCount }}</p>
                </div>
            </div>
        </x-card>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('admin.ops.index', ['tab' => 'deposits']) }}"
                class="rounded-full border px-4 py-2 transition {{ $tab === 'deposits' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">طلبات الشحن المعلقة</a>
            <a href="{{ route('admin.ops.index', ['tab' => 'orders_new']) }}"
                class="rounded-full border px-4 py-2 transition {{ $tab === 'orders_new' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">طلبات جديدة</a>
            <a href="{{ route('admin.ops.index', ['tab' => 'orders_processing']) }}"
                class="rounded-full border px-4 py-2 transition {{ $tab === 'orders_processing' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">طلبات قيد التنفيذ</a>
        </div>

        @if ($tab === 'deposits')
            <x-card :hover="false" class="p-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-900">طلبات الشحن</h2>
                    <form method="GET" action="{{ route('admin.ops.index') }}" class="flex flex-wrap items-center gap-2 text-sm">
                        <input type="hidden" name="tab" value="deposits" />
                        <x-text-input name="deposit_q" value="{{ $depositSearch }}" placeholder="بحث بالاسم أو البريد أو الرقم" />
                        <x-select name="deposit_status">
                            <option value="pending" @selected($depositStatus === 'pending')>قيد المراجعة</option>
                            <option value="approved" @selected($depositStatus === 'approved')>مقبول</option>
                            <option value="rejected" @selected($depositStatus === 'rejected')>مرفوض</option>
                        </x-select>
                        <x-text-input type="date" name="deposit_from" value="{{ $depositFrom }}" />
                        <x-text-input type="date" name="deposit_to" value="{{ $depositTo }}" />
                        <x-button type="submit">تطبيق</x-button>
                    </form>
                </div>

                <x-table class="mt-6">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="py-2">رقم الطلب</th>
                            <th class="py-2">المستخدم</th>
                            <th class="py-2">الطريقة</th>
                            <th class="py-2">المبلغ</th>
                            <th class="py-2">الحالة</th>
                            <th class="py-2">التاريخ</th>
                            <th class="py-2">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($deposits as $deposit)
                            <tr class="transition hover:bg-slate-50">
                                <td class="py-3 text-slate-500">#{{ $deposit->id }}</td>
                                <td class="py-3 text-slate-700">
                                    {{ $deposit->user->name }}
                                    <div class="text-xs text-slate-500">{{ $deposit->user->email }}</div>
                                </td>
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
                                <td class="py-3 text-slate-500">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-500">لا توجد طلبات مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-6">{{ $deposits->links() }}</div>
            </x-card>
        @else
            <x-card :hover="false" class="p-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-900">طلبات الخدمات</h2>
                    <form method="GET" action="{{ route('admin.ops.index') }}" class="flex flex-wrap items-center gap-2 text-sm">
                        <input type="hidden" name="tab" value="{{ $tab }}" />
                        <x-text-input name="order_q" value="{{ $orderSearch }}" placeholder="بحث بالاسم أو البريد أو الرقم" />
                        <x-text-input type="date" name="order_from" value="{{ $orderFrom }}" />
                        <x-text-input type="date" name="order_to" value="{{ $orderTo }}" />
                        <x-button type="submit">تطبيق</x-button>
                    </form>
                </div>

                <x-table class="mt-6">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="py-2">رقم الطلب</th>
                            <th class="py-2">المستخدم</th>
                            <th class="py-2">الخدمة</th>
                            <th class="py-2">الباقة</th>
                            <th class="py-2">المبلغ المعلّق</th>
                            <th class="py-2">الحالة</th>
                            <th class="py-2">التاريخ</th>
                            <th class="py-2">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-slate-50">
                                <td class="py-3 text-slate-500">#{{ $order->id }}</td>
                                <td class="py-3 text-slate-700">
                                    {{ $order->user->name }}
                                    <div class="text-xs text-slate-500">{{ $order->user->email }}</div>
                                </td>
                                <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                                <td class="py-3 text-slate-700">{{ $order->variant?->name ?? '-' }}</td>
                                <td class="py-3 text-slate-700">{{ number_format($order->amount_held, 2) }} USD</td>
                                <td class="py-3">
                                    @if ($order->status === 'new')
                                        <x-badge type="new">جديد</x-badge>
                                    @elseif ($order->status === 'processing')
                                        <x-badge type="processing">قيد التنفيذ</x-badge>
                                    @elseif ($order->status === 'done')
                                        <x-badge type="done">تم التنفيذ</x-badge>
                                    @else
                                        <x-badge type="rejected">مرفوض</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3">
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                        @if ($order->status === 'new')
                                            <form method="POST" action="{{ route('admin.ops.orders.start-processing', $order) }}" onsubmit="return confirm('هل أنت متأكد من بدء تنفيذ الطلب؟')">
                                                @csrf
                                                <input type="hidden" name="status" value="processing" />
                                                <button type="submit" class="text-blue-700 hover:text-blue-900">بدء التنفيذ</button>
                                            </form>
                                        @elseif ($order->status === 'processing')
                                            <form method="POST" action="{{ route('admin.ops.orders.mark-done', $order) }}" onsubmit="return confirm('هل أنت متأكد من اعتماد التنفيذ؟')">
                                                @csrf
                                                <input type="hidden" name="status" value="done" />
                                                <button type="submit" class="text-emerald-700 hover:text-emerald-900">تم التنفيذ</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.ops.orders.reject', $order) }}" onsubmit="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected" />
                                                <button type="submit" class="text-rose-700 hover:text-rose-900">رفض</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-slate-500">لا توجد طلبات مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-6">{{ $orders->links() }}</div>
            </x-card>
        @endif
    </div>
@endsection
