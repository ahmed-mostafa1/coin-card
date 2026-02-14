@extends('layouts.app')

@section('title', 'لوحة العمليات')
@section('mainWidth', 'container-fluid w-full max-w-[98%]')

@section('content')
    <div class="space-y-6">
        <x-card :hover="false">
            <x-page-header title="لوحة العمليات" subtitle="إدارة سريعة للطلبات وطلبات الشحن." />

            <div class="mt-6 grid gap-4 grid-cols-2 md:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">طلبات الشحن</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ $pendingDepositsCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">طلبات جديدة</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ $newOrdersCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">طلبات تحت التنفيذ</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ $processingOrdersCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">طلبات منتهية</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ $doneOrdersCount }}</p>
                </div>
            </div>
        </x-card>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('admin.ops.index', ['tab' => 'orders_new']) }}"
                class="rounded-full border px-4 py-2 transition {{ $tab === 'orders_new' ? 'border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">طلبات جديدة</a>
            <a href="{{ route('admin.ops.index', ['tab' => 'orders_processing']) }}"
                class="rounded-full border px-4 py-2 transition {{ $tab === 'orders_processing' ? 'border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">طلبات تحت التنفيذ</a>
            <a href="{{ route('admin.ops.index', ['tab' => 'orders_done']) }}"
                class="rounded-full border px-4 py-2 transition {{ $tab === 'orders_done' ? 'border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">طلبات منتهية</a>
        </div>

        @if ($tab === 'deposits')
            <x-card :hover="false" class="p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">طلبات الشحن</h2>
                    <form method="GET" action="{{ route('admin.ops.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap items-stretch sm:items-center gap-2 text-sm w-full lg:w-auto">
                        <input type="hidden" name="tab" value="deposits" />
                        <x-text-input name="deposit_q" value="{{ $depositSearch }}" placeholder=".." class="lg:w-auto" />
                        <x-select name="deposit_status" class="lg:w-auto">
                            <option value="pending" @selected($depositStatus === 'pending')>قيد المراجعة</option>
                            <option value="approved" @selected($depositStatus === 'approved')>مقبول</option>
                            <option value="rejected" @selected($depositStatus === 'rejected')>مرفوض</option>
                        </x-select>
                        <x-text-input type="date" name="deposit_from" value="{{ $depositFrom }}" class="lg:w-auto" />
                        <x-text-input type="date" name="deposit_to" value="{{ $depositTo }}" class="lg:w-auto" />
                        <x-button type="submit" class="lg:w-auto">تطبيق</x-button>
                    </form>
                </div>

                <x-table class="mt-6">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
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
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($deposits as $deposit)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="py-3 text-slate-500 dark:text-slate-400" data-label="رقم الطلب">#{{ $deposit->id }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="المستخدم">
                                    {{ $deposit->user->name }}
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $deposit->user->email }}</div>
                                </td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="الطريقة">{{ $deposit->paymentMethod->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="المبلغ">{{ number_format($deposit->user_amount, 2) }} USD</td>
                                <td class="py-3" data-label="الحالة">
                                    @if ($deposit->status === 'pending')
                                        <x-badge type="pending">قيد المراجعة</x-badge>
                                    @elseif ($deposit->status === 'approved')
                                        <x-badge type="approved">مقبول</x-badge>
                                    @else
                                        <x-badge type="rejected">مرفوض</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400" data-label="التاريخ">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3" data-label="عرض">
                                    <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد طلبات مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-6">{{ $deposits->links() }}</div>
            </x-card>
        @else
            <x-card :hover="false" class="p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">طلبات الخدمات</h2>
                    <form method="GET" action="{{ route('admin.ops.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap items-stretch sm:items-center gap-2 text-sm w-full lg:w-auto">
                        <input type="hidden" name="tab" value="{{ $tab }}" />
                        <x-text-input name="order_q" value="{{ $orderSearch }}" placeholder=".." class="lg:w-auto" />
                        <x-text-input type="date" name="order_from" value="{{ $orderFrom }}" class="lg:w-auto" />
                        <x-text-input type="date" name="order_to" value="{{ $orderTo }}" class="lg:w-auto" />
                        <x-button type="submit" class="lg:w-auto">تطبيق</x-button>
                    </form>
                </div>

                <x-table class="mt-6">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
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
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="py-3 text-slate-500 dark:text-slate-400" data-label="رقم الطلب">#{{ $order->id }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="المستخدم">
                                    {{ $order->user->name }}
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->user->email }}</div>
                                </td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="الخدمة">{{ $order->service->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="الباقة">{{ $order->variant?->name ?? '-' }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="المبلغ المعلّق">{{ number_format($order->amount_held, 2) }} USD</td>
                                <td class="py-3" data-label="الحالة">
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
                                <td class="py-3 text-slate-500 dark:text-slate-400" data-label="التاريخ">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3" data-label="الإجراء">
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">عرض</a>
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
                                                <button type="submit" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تم التنفيذ</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.ops.orders.reject', $order) }}" onsubmit="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected" />
                                                <button type="submit" class="text-rose-700 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300">رفض</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد طلبات مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>

                <div class="mt-6">{{ $orders->links() }}</div>
            </x-card>
        @endif
    </div>
@endsection