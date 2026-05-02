@extends('layouts.app')

@section('title', 'تفاصيل طلب الشحن')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-100">طلب شحن #{{ $depositRequest->id }}</h1>
                    <p class="mt-2 text-sm text-slate-900 dark:text-slate-50">تم الإنشاء في {{ $depositRequest->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <a href="{{ route('admin.deposits.index') }}" class="text-sm text-emerald-900 hover:text-emerald-900 dark:text-emerald-100">عودة للقائمة</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                    <p class="text-xs text-slate-700 dark:text-slate-50">المستخدم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-50">{{ $depositRequest->user?->name ?? 'مستخدم محذوف' }}</p>
                    <p class="text-xs text-slate-700 dark:text-slate-50">{{ $depositRequest->user?->email }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                    <p class="text-xs text-slate-700 dark:text-slate-50">طريقة الدفع</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-50">{{ $depositRequest->paymentMethod?->name ?? 'طريقة محذوفة' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                    <p class="text-xs text-slate-700 dark:text-slate-50">المبلغ المطلوب</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-50">{{ number_format($depositRequest->net_usd_amount ?? $depositRequest->user_amount, 2) }} USD</p>
                    @if ($depositRequest->currency_code)
                        <p class="mt-1 text-xs text-slate-900 dark:text-slate-50">{{ number_format($depositRequest->local_amount, 2) }} {{ $depositRequest->currency_code }} بسعر {{ rtrim(rtrim(number_format($depositRequest->exchange_rate_to_usd, 8, '.', ''), '0'), '.') }}</p>
                    @endif
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                    <p class="text-xs text-slate-700 dark:text-slate-50">المبلغ المعتمد</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-50">
                        {{ $depositRequest->approved_amount ? number_format($depositRequest->approved_amount, 2) : '-' }} USD
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                    <p class="text-xs text-slate-700 dark:text-slate-50">الحالة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-50">
                        @if ($depositRequest->status === 'pending')
                            قيد المراجعة
                        @elseif ($depositRequest->status === 'approved')
                            مقبول
                        @else
                            مرفوض
                        @endif
                    </p>
                </div>
                @if ($depositRequest->currency_code)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                        <p class="text-xs text-slate-700 dark:text-slate-50">العمولة</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-50">{{ number_format($depositRequest->commission_amount, 2) }} {{ $depositRequest->currency_code }}</p>
                        <p class="text-xs text-slate-700 dark:text-slate-50">{{ $depositRequest->commission_type === 'fixed' ? 'ثابتة' : 'نسبة' }}: {{ rtrim(rtrim(number_format($depositRequest->commission_value, 4, '.', ''), '0'), '.') }}</p>
                    </div>
                @endif
            </div>


            @if ($depositRequest->paymentMethod && $depositRequest->paymentMethod->fields->isNotEmpty())
                <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                    <p class="text-xs text-slate-700 dark:text-slate-50">{{ __('messages.additional_details_label') }}</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach ($depositRequest->paymentMethod->fields->sortBy('sort_order') as $field)
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs text-slate-700 dark:text-slate-50">{{ $field->label }}</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ ($depositRequest->payload ?? [])[$field->name_key] ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4">
                <p class="text-xs text-slate-700 dark:text-slate-50">إثبات التحويل</p>
                @if ($depositRequest->evidence)
                    @if (str_starts_with($depositRequest->evidence->mime, 'image/'))
                        <img src="{{ route('admin.deposits.evidence', $depositRequest) }}" alt="إثبات التحويل" class="mt-3 max-h-64 rounded-2xl border border-slate-200 dark:border-slate-700 object-contain">
                    @else
                        <a href="{{ route('admin.deposits.evidence', $depositRequest) }}" class="mt-3 inline-flex rounded-full border border-emerald-200 dark:border-emerald-700 px-4 py-2 text-sm font-semibold text-emerald-900 dark:text-emerald-400">تحميل ملف الإثبات</a>
                    @endif
                @else
                    <p class="mt-2 text-sm text-slate-500">لا يوجد ملف مرفق.</p>
                @endif
            </div>

            @if ($depositRequest->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 bg-emerald-50 dark:bg-emerald-900/20 p-4 text-sm text-emerald-900 dark:text-emerald-400">
                    ملاحظة الإدارة: {{ $depositRequest->admin_note }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">إجراءات الطلب</h2>

            @if ($errors->has('status'))
                <div class="mt-4 rounded-lg border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm text-rose-700 dark:text-rose-400">
                    {{ $errors->first('status') }}
                </div>
            @endif

            @if ($depositRequest->status === 'pending')
                <form method="POST" action="{{ route('admin.deposits.approve', $depositRequest) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="approved_amount" value="المبلغ المعتمد" />
                        <x-text-input id="approved_amount" name="approved_amount" type="number" step="0.01" min="1" :value="old('approved_amount', $depositRequest->net_usd_amount ?? $depositRequest->user_amount)" required />
                        <x-input-error :messages="$errors->get('approved_amount')" />
                    </div>
                    <div>
                        <x-input-label for="admin_note" value="ملاحظة (اختياري)" />
                        <textarea id="admin_note" name="admin_note" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-50">{{ old('admin_note') }}</textarea>
                        <x-input-error :messages="$errors->get('admin_note')" />
                    </div>
                    <x-primary-button class="w-full">اعتماد الطلب</x-primary-button>
                </form>

                <form method="POST" action="{{ route('admin.deposits.reject', $depositRequest) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="reject_note" value="سبب الرفض" />
                        <textarea id="reject_note" name="admin_note" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-50">{{ old('admin_note') }}</textarea>
                        <x-input-error :messages="$errors->get('admin_note')" />
                    </div>
                    <button type="submit" class="w-full rounded-full border border-rose-200 dark:border-rose-700 px-4 py-2 text-sm font-semibold text-rose-600 dark:text-rose-400 transition hover:bg-rose-50 dark:hover:bg-rose-900/30">رفض الطلب</button>
                </form>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300">
                    تم اتخاذ قرار على هذا الطلب.
                </div>
            @endif
        </div>
    </div>
@endsection
