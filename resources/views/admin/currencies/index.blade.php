@extends('layouts.app')

@section('title', 'العملات')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <x-card :hover="false" class="p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <x-page-header title="العملات" subtitle="إدارة العملات وسعر تحويلها إلى USD." />
            <a href="{{ route('admin.currencies.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">إضافة عملة</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-100">{{ session('status') }}</div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-50 text-slate-900 dark:border-slate-700 dark:text-slate-100">
                <tr>
                    <th class="py-2">الاسم</th>
                    <th class="py-2">الكود</th>
                    <th class="py-2">الرمز</th>
                    <th class="py-2">سعر التحويل إلى USD</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                @forelse ($currencies as $currency)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $currency->name }}</td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $currency->code }}</td>
                        <td class="py-3 text-slate-900 dark:text-slate-100">{{ $currency->symbol ?: '-' }}</td>
                        <td class="py-3 text-slate-700 dark:text-white" dir="ltr">{{ rtrim(rtrim(number_format($currency->exchange_rate_to_usd, 8, '.', ''), '0'), '.') }}</td>
                        <td class="py-3">
                            @if ($currency->is_enabled)
                                <x-badge type="approved">مفعلة</x-badge>
                            @else
                                <x-badge type="rejected">معطلة</x-badge>
                            @endif
                        </td>
                        <td class="py-3"><a href="{{ route('admin.currencies.edit', $currency) }}" class="inline-flex rounded-lg border border-emerald-200 dark:border-emerald-700 px-2.5 py-1 text-xs font-semibold text-emerald-900 hover:text-emerald-900 dark:text-emerald-100">تعديل</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-slate-900">لا توجد عملات.</td></tr>
                @endforelse
            </tbody>
        </x-table>
    </x-card>
@endsection
