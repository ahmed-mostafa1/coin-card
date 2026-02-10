@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">طلب #{{ $order->id }}</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $order->service->name }}</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-emerald-700 dark:text-emerald-400">عودة للقائمة</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">المستخدم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $order->user->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $order->user->email }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">السعر</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ number_format($order->price_at_purchase, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">المبلغ المعلّق</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ number_format($order->amount_held, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">الحالة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                        @if ($order->status === 'new')
                            جديد
                        @elseif ($order->status === 'processing')
                            قيد التنفيذ
                        @elseif ($order->status === 'done')
                            تم التنفيذ
                        @elseif ($order->status === 'rejected')
                            مرفوض
                        @else
                            ملغي
                        @endif
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">الباقة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $order->variant?->name ?? 'السعر الأساسي' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">تأكيد الخصم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $order->settled_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">إرجاع الرصيد</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $order->released_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                <p class="text-xs text-slate-500 dark:text-slate-400">بيانات الطلب</p>
                @if (count($order->payload))
                    <div class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                        @foreach ($order->payload as $key => $value)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400">{{ $fieldLabels[$key] ?? $key }}</span>
                                <span class="font-semibold">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">لا توجد بيانات إضافية.</p>
                @endif
            </div>

            @if ($order->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 p-4 text-sm text-emerald-700 dark:text-emerald-300">
                    ملاحظة الإدارة: {{ $order->admin_note }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">تحديث الحالة</h2>

            @if (session('status'))
                <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="status" value="الحالة" />
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white">
                        <option value="new" @selected($order->status === 'new')>جديد</option>
                        <option value="processing" @selected($order->status === 'processing')>قيد التنفيذ</option>
                        <option value="done" @selected($order->status === 'done')>تم التنفيذ</option>
                        <option value="rejected" @selected($order->status === 'rejected')>مرفوض</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="admin_note" value="ملاحظة" />
                    <textarea id="admin_note" name="admin_note" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white">{{ old('admin_note', $order->admin_note) }}</textarea>
                </div>

                <x-primary-button class="w-full">حفظ التحديث</x-primary-button>
            </form>

            <div class="mt-8 border-t border-slate-200 dark:border-slate-700 pt-6">
                <h3 class="text-base font-semibold text-emerald-700 dark:text-emerald-400">سجل الطلب</h3>
                @php
                    $statusLabels = [
                        'new' => 'جديد',
                        'processing' => 'قيد التنفيذ',
                        'done' => 'تم التنفيذ',
                        'rejected' => 'مرفوض',
                        'cancelled' => 'ملغي',
                    ];
                @endphp
                <div class="mt-4 space-y-4">
                    @forelse ($order->events as $event)
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $event->message ?? 'تحديث جديد' }}</p>
                                <span class="text-xs text-slate-400">{{ $event->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">الجهة: {{ $event->actor?->name ?? 'النظام' }}</p>
                            @if ($event->old_status || $event->new_status)
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    الحالة: {{ $statusLabels[$event->old_status] ?? '-' }} → {{ $statusLabels[$event->new_status] ?? '-' }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد تحديثات مسجلة بعد.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
