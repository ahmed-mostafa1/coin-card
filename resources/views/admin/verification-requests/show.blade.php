@extends('layouts.app')
@section('title', 'طلب توثيق')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card :hover="false" class="p-8 lg:col-span-2">
            <x-page-header title="طلب توثيق #{{ $verificationRequest->id }}" subtitle="{{ $verificationRequest->user?->name }} - {{ $verificationRequest->status }}" />
            @if (session('status'))<div class="mt-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-300">{{ session('status') }}</div>@endif
            <div class="mt-6 space-y-3">
                @foreach (($verificationRequest->payload ?? []) as $key => $value)
                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70"><p class="text-xs text-slate-9000 dark:text-slate-50">{{ $key }}</p><p class="mt-1 font-semibold text-slate-800 dark:text-white break-words">{{ $value }}</p></div>
                @endforeach
                @foreach ($verificationRequest->files as $file)
                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70"><p class="text-xs text-slate-9000 dark:text-slate-50">{{ $file->field?->label ?? $file->field_key }}</p><a href="{{ route('admin.verification-requests.files.show', [$verificationRequest, $file->id]) }}" class="mt-2 inline-flex rounded-full border border-emerald-200 dark:border-emerald-700 px-4 py-2 text-sm font-semibold text-emerald-900 dark:text-emerald-300">عرض/تحميل الملف</a></div>
                @endforeach
            </div>
        </x-card>
        <x-card :hover="false" class="p-8">
            <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">المراجعة</h2>
            <form method="POST" action="{{ route('admin.verification-requests.approve', $verificationRequest) }}" class="mt-4 space-y-4">
                @csrf
                <x-input-label for="assigned_discount_percentage" value="خصم المستخدم (%)" />
                <x-text-input id="assigned_discount_percentage" name="assigned_discount_percentage" type="number" min="0" max="100" step="0.01" :value="old('assigned_discount_percentage', $verificationRequest->assigned_discount_percentage)" />
                <textarea name="review_note" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-50" placeholder="ملاحظة المراجعة">{{ old('review_note') }}</textarea>
                <x-primary-button class="w-full">اعتماد التوثيق</x-primary-button>
            </form>
            <form method="POST" action="{{ route('admin.verification-requests.status', $verificationRequest) }}" class="mt-6 space-y-4">
                @csrf
                <x-select name="status"><option value="needs_changes">يحتاج تعديلات</option><option value="rejected">مرفوض</option></x-select>
                <textarea name="review_note" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-50" placeholder="ملاحظة للمستخدم"></textarea>
                <button class="w-full rounded-full border border-amber-200 dark:border-amber-700 px-4 py-2 text-sm font-semibold text-amber-700 dark:text-amber-300">تحديث الحالة</button>
            </form>
        </x-card>
    </div>
@endsection
