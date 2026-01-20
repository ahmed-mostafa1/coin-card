@extends('layouts.app')

@section('title', 'لوحة العمليات')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">لوحة العمليات</h1>
                    <p class="mt-2 text-sm text-slate-600">قوائم سريعة لطلبات الشحن والطلبات الجديدة.</p>
                </div>
                <form method="GET" action="{{ route('admin.ops.index') }}" class="flex w-full max-w-md items-center gap-2">
                    <input type="text" name="q" value="{{ $search }}" placeholder="بحث باسم المستخدم أو البريد"
                        class="w-full rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700" />
                    <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        بحث
                    </button>
                </form>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700">طلبات الشحن</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs text-emerald-700">{{ $depositCounts[\App\Models\DepositRequest::STATUS_PENDING] ?? 0 }} معلق</span>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <a href="{{ route('admin.ops.index', array_filter(['deposit_status' => 'pending', 'order_status' => $orderStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $depositStatus === 'pending' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        قيد المراجعة
                    </a>
                    <a href="{{ route('admin.ops.index', array_filter(['deposit_status' => 'approved', 'order_status' => $orderStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $depositStatus === 'approved' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        معتمد
                    </a>
                    <a href="{{ route('admin.ops.index', array_filter(['deposit_status' => 'rejected', 'order_status' => $orderStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $depositStatus === 'rejected' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        مرفوض
                    </a>
                </div>
                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="border-b border-slate-200 text-xs text-slate-500">
                            <tr>
                                <th class="py-2">التاريخ</th>
                                <th class="py-2">المستخدم</th>
                                <th class="py-2">الطريقة</th>
                                <th class="py-2">المبلغ</th>
                                <th class="py-2">الحالة</th>
                                <th class="py-2">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($deposits as $deposit)
                                <tr>
                                    <td class="py-3 text-slate-500">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 text-slate-700">{{ $deposit->user->name }}<div class="text-xs text-slate-500">{{ $deposit->user->email }}</div></td>
                                    <td class="py-3 text-slate-700">{{ $deposit->paymentMethod->name }}</td>
                                    <td class="py-3 text-slate-700">{{ number_format($deposit->user_amount, 2) }} ر.س</td>
                                    <td class="py-3">
                                        @if ($deposit->status === 'pending')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">قيد المراجعة</span>
                                        @elseif ($deposit->status === 'approved')
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">معتمد</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.deposits.show', $deposit) }}" class="text-emerald-700 hover:text-emerald-900">مراجعة</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-slate-500">لا توجد طلبات مطابقة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-emerald-700">طلبات الخدمات</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs text-emerald-700">{{ ($orderCounts[\App\Models\Order::STATUS_NEW] ?? 0) + ($orderCounts[\App\Models\Order::STATUS_PROCESSING] ?? 0) }} نشطة</span>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <a href="{{ route('admin.ops.index', array_filter(['order_status' => 'new', 'deposit_status' => $depositStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $orderStatus === 'new' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        جديد
                    </a>
                    <a href="{{ route('admin.ops.index', array_filter(['order_status' => 'processing', 'deposit_status' => $depositStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $orderStatus === 'processing' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        قيد التنفيذ
                    </a>
                    <a href="{{ route('admin.ops.index', array_filter(['order_status' => 'done', 'deposit_status' => $depositStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $orderStatus === 'done' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        تم التنفيذ
                    </a>
                    <a href="{{ route('admin.ops.index', array_filter(['order_status' => 'rejected', 'deposit_status' => $depositStatus, 'q' => $search])) }}"
                        class="rounded-full border px-3 py-1 transition {{ $orderStatus === 'rejected' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">
                        مرفوض
                    </a>
                </div>
                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="border-b border-slate-200 text-xs text-slate-500">
                            <tr>
                                <th class="py-2">التاريخ</th>
                                <th class="py-2">المستخدم</th>
                                <th class="py-2">الخدمة</th>
                                <th class="py-2">المبلغ</th>
                                <th class="py-2">الحالة</th>
                                <th class="py-2">إجراء سريع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="py-3 text-slate-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 text-slate-700">{{ $order->user->name }}<div class="text-xs text-slate-500">{{ $order->user->email }}</div></td>
                                    <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                                    <td class="py-3 text-slate-700">{{ number_format($order->amount_held, 2) }} ر.س</td>
                                    <td class="py-3">
                                        @if ($order->status === 'new')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">جديد</span>
                                        @elseif ($order->status === 'processing')
                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">قيد التنفيذ</span>
                                        @elseif ($order->status === 'done')
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">تم التنفيذ</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-wrap gap-2 text-xs">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                            @if ($order->status === 'new')
                                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" onsubmit="return confirm('هل أنت متأكد من تحديث الحالة؟')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="admin_note" value="{{ $order->admin_note }}" />
                                                    <input type="hidden" name="status" value="processing" />
                                                    <button type="submit" class="text-blue-700 hover:text-blue-900">قيد التنفيذ</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" onsubmit="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="admin_note" value="{{ $order->admin_note }}" />
                                                    <input type="hidden" name="status" value="rejected" />
                                                    <button type="submit" class="text-rose-700 hover:text-rose-900">رفض</button>
                                                </form>
                                            @elseif ($order->status === 'processing')
                                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" onsubmit="return confirm('هل أنت متأكد من اعتماد التنفيذ؟')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="admin_note" value="{{ $order->admin_note }}" />
                                                    <input type="hidden" name="status" value="done" />
                                                    <button type="submit" class="text-emerald-700 hover:text-emerald-900">تم التنفيذ</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" onsubmit="return confirm('هل أنت متأكد من رفض الطلب؟')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="admin_note" value="{{ $order->admin_note }}" />
                                                    <input type="hidden" name="status" value="rejected" />
                                                    <button type="submit" class="text-rose-700 hover:text-rose-900">رفض</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-slate-500">لا توجد طلبات مطابقة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
