@extends('layouts.app')

@section('title', 'طرق الدفع')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-100">طرق الدفع</h1>
                <p class="mt-2 text-sm text-slate-900 dark:text-slate-50 dark:text-slate-50">إدارة طرق الشحن اليدوية.</p>
            </div>
            <a href="{{ route('admin.payment-methods.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة طريقة</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-50 dark:border-slate-700 text-slate-900 dark:text-slate-100">
                <tr>
                    <th class="py-2">الاسم</th>
                    <th class="py-2">المعرف</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">الترتيب</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                @forelse ($methods as $method)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $method->name }}</td>
                        <td class="py-3 text-slate-900 dark:text-slate-100">{{ $method->slug }}</td>
                        <td class="py-3">
                            @if ($method->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-900 dark:text-emerald-100">مفعل</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">متوقف</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-900 dark:text-slate-100">{{ $method->sort_order }}</td>
                        <td class="py-3">
                            <a href="{{ route('admin.payment-methods.edit', $method) }}" class="text-emerald-900 dark:text-emerald-100 hover:text-emerald-900 dark:hover:text-emerald-100">تعديل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-900 dark:text-slate-100">لا توجد طرق دفع بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
