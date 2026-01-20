@extends('layouts.app')

@section('title', 'تفاصيل طلب الشحن')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">طلب شحن #{{ $depositRequest->id }}</h1>
                    <p class="mt-2 text-sm text-slate-600">تم الإنشاء في {{ $depositRequest->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <a href="{{ route('admin.deposits.index') }}" class="text-sm text-emerald-700 hover:text-emerald-900">عودة للقائمة</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">المستخدم</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $depositRequest->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $depositRequest->user->email }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">طريقة الدفع</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $depositRequest->paymentMethod->name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">المبلغ المطلوب</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($depositRequest->user_amount, 2) }} ر.س</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">المبلغ المعتمد</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        {{ $depositRequest->approved_amount ? number_format($depositRequest->approved_amount, 2) : '-' }} ر.س
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">الحالة</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        @if ($depositRequest->status === 'pending')
                            قيد المراجعة
                        @elseif ($depositRequest->status === 'approved')
                            مقبول
                        @else
                            مرفوض
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">إثبات التحويل</p>
                @if ($depositRequest->evidence)
                    @if (str_starts_with($depositRequest->evidence->mime, 'image/'))
                        <img src="{{ route('admin.deposits.evidence', $depositRequest) }}" alt="إثبات التحويل" class="mt-3 max-h-64 rounded-2xl border border-slate-200 object-contain">
                    @else
                        <a href="{{ route('admin.deposits.evidence', $depositRequest) }}" class="mt-3 inline-flex rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700">تحميل ملف الإثبات</a>
                    @endif
                @else
                    <p class="mt-2 text-sm text-slate-500">لا يوجد ملف مرفق.</p>
                @endif
            </div>

            @if ($depositRequest->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-700">
                    ملاحظة الإدارة: {{ $depositRequest->admin_note }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">إجراءات الطلب</h2>

            @if ($errors->has('status'))
                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first('status') }}
                </div>
            @endif

            @if ($depositRequest->status === 'pending')
                <form method="POST" action="{{ route('admin.deposits.approve', $depositRequest) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="approved_amount" value="المبلغ المعتمد" />
                        <x-text-input id="approved_amount" name="approved_amount" type="number" step="0.01" min="1" :value="old('approved_amount', $depositRequest->user_amount)" required />
                        <x-input-error :messages="$errors->get('approved_amount')" />
                    </div>
                    <div>
                        <x-input-label for="admin_note" value="ملاحظة (اختياري)" />
                        <textarea id="admin_note" name="admin_note" rows="3" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('admin_note') }}</textarea>
                        <x-input-error :messages="$errors->get('admin_note')" />
                    </div>
                    <x-primary-button class="w-full">اعتماد الطلب</x-primary-button>
                </form>

                <form method="POST" action="{{ route('admin.deposits.reject', $depositRequest) }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="reject_note" value="سبب الرفض" />
                        <textarea id="reject_note" name="admin_note" rows="3" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700">{{ old('admin_note') }}</textarea>
                        <x-input-error :messages="$errors->get('admin_note')" />
                    </div>
                    <button type="submit" class="w-full rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">رفض الطلب</button>
                </form>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 p-4 text-sm text-slate-600">
                    تم اتخاذ قرار على هذا الطلب.
                </div>
            @endif
        </div>
    </div>
@endsection
