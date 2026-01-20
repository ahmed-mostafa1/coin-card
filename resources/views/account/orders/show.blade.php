@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-8 lg:col-span-2" :hover="false">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">طلب #{{ $order->id }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ $order->service->name }}</p>
                </div>
                <a href="{{ route('account.orders') }}" class="text-sm text-emerald-700 hover:text-emerald-900">عودة للطلبات</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">السعر</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($order->price_at_purchase, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">المبلغ المعلّق</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($order->amount_held, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">الحالة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
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
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">الباقة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->variant?->name ?? 'السعر الأساسي' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">التاريخ</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">تأكيد الخصم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->settled_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">إرجاع الرصيد</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->released_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">بيانات الطلب</p>
                @if (count($order->payload))
                    <div class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach ($order->payload as $key => $value)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">{{ $fieldLabels[$key] ?? $key }}</span>
                                <span class="font-semibold">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">لا توجد بيانات إضافية.</p>
                @endif
            </div>

            @if ($order->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-700">
                    ملاحظة الإدارة: {{ $order->admin_note }}
                </div>
            @endif
        </x-card>

        <x-card class="p-8" :hover="false">
            <h2 class="text-lg font-semibold text-emerald-700">سجل الطلب</h2>
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
                    @php
                        $actorLabel = 'النظام';
                        if ($event->actor) {
                            $actorLabel = $event->actor->id === auth()->id() ? 'أنت' : 'الإدارة';
                        }
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-700">{{ $event->message ?? 'تحديث جديد' }}</p>
                            <span class="text-xs text-slate-400">{{ $event->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">الجهة: {{ $actorLabel }}</p>
                        @if ($event->old_status || $event->new_status)
                            <p class="mt-1 text-xs text-slate-500">
                                الحالة: {{ $statusLabels[$event->old_status] ?? '-' }} → {{ $statusLabels[$event->new_status] ?? '-' }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">لا توجد تحديثات مسجلة بعد.</p>
                @endforelse
            </div>
        </x-card>
    </div>
@endsection
