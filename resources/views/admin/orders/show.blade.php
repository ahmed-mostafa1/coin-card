@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">طلب #{{ $order->id }}</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $order->service?->name ?? 'خدمة محذوفة' }}</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-emerald-700 dark:text-emerald-400">عودة للقائمة</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">المستخدم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $order->user?->name ?? 'مستخدم محذوف' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $order->user?->email }}</p>
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
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('messages.order_data_label') }}</p>
                @if (count($order->payload))
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach ($order->payload as $key => $value)
                            @php
                                $displayValue = is_scalar($value) || $value === null
                                    ? (string) $value
                                    : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                $displayValue = trim((string) $displayValue);

                                if ($key === 'service_discount_percent' && is_numeric($displayValue)) {
                                    $displayValue = number_format((float) $displayValue, 2) . '%';
                                }

                                if (in_array($key, ['offer_amount', 'payable_after_discount'], true) && is_numeric($displayValue)) {
                                    $displayValue = number_format((float) $displayValue, 2) . ' USD';
                                }

                                $isUrl = filter_var($displayValue, FILTER_VALIDATE_URL) !== false;
                                $isImageByExt = preg_match('/\.(jpg|jpeg|png|webp|gif|bmp|svg)(\?.*)?$/i', $displayValue) === 1;
                                $isImagePath = ! $isUrl
                                    && $isImageByExt
                                    && !\Illuminate\Support\Str::contains($displayValue, [' ', "\n", "\r", "\t"]);

                                $imageUrl = null;
                                if ($isUrl && $isImageByExt) {
                                    $imageUrl = $displayValue;
                                } elseif ($isImagePath) {
                                    $imageUrl = asset('storage/' . ltrim($displayValue, '/'));
                                }
                            @endphp
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/50 p-3">
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $fieldLabels[$key] ?? \Illuminate\Support\Str::headline((string) $key) }}</p>

                                @if ($imageUrl)
                                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block">
                                        <img src="{{ $imageUrl }}" alt="{{ $fieldLabels[$key] ?? (string) $key }}" class="h-40 w-auto max-w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 object-contain">
                                    </a>
                                @elseif ($isUrl)
                                    <a href="{{ $displayValue }}" target="_blank" rel="noopener noreferrer" class="mt-2 block break-all text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:underline">
                                        {{ $displayValue }}
                                    </a>
                                @else
                                    <p class="mt-2 break-words text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        {{ $displayValue !== '' ? $displayValue : '-' }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.no_additional_data') }}</p>
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

            {{-- DailyCard Fulfillment Section --}}
            @if ($order->service && $order->service->source === 'dailycard')
                <div class="mt-6 rounded-2xl border border-blue-100 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/20 p-4">
                    <h3 class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-3 flex items-center gap-2">
                        <span>🔗</span> تنفيذ DailyCard
                    </h3>

                    <div class="space-y-2 text-xs text-slate-600 dark:text-slate-300">
                        <div class="flex justify-between">
                            <span>رقم المعاملة</span>
                            <span class="font-mono font-semibold">{{ $order->provider_transaction_id ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>حالة التنفيذ</span>
                            @php
                                $execStatus = $order->provider_execution_status ?? null;
                                $execColor = match($execStatus) {
                                    'success', 'completed' => 'text-emerald-700 bg-emerald-100',
                                    'failed', 'error'      => 'text-red-700 bg-red-100',
                                    'processing'           => 'text-amber-700 bg-amber-100',
                                    default                => 'text-gray-600 bg-gray-100',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $execColor }}">
                                {{ $execStatus ?? 'لم يُرسل بعد' }}
                            </span>
                        </div>
                        @if($order->provider_replay)
                            <div class="pt-2 border-t border-blue-100 dark:border-blue-800">
                                <p class="text-[11px] text-gray-500 mb-1">نتيجة التنفيذ</p>
                                <p class="text-xs text-slate-700 dark:text-slate-200 font-medium break-words">{{ $order->provider_replay }}</p>
                            </div>
                        @endif
                    </div>

                    @if(!in_array($order->status, ['done', 'rejected']))
                        <form method="POST" action="{{ route('admin.orders.sync-provider', $order) }}" class="mt-4">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('تحديث حالة الطلب من DailyCard؟')"
                                    class="w-full px-4 py-2 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                🔄 تحديث الحالة من DailyCard
                            </button>
                        </form>
                    @endif
                </div>
            @endif

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
