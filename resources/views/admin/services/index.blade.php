@extends('layouts.app')

@section('title', 'الخدمات')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">الخدمات</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">إدارة الخدمات والأسعار.</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة خدمة</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="py-2">الاسم</th>
                        <th class="py-2">التصنيف</th>
                        <th class="py-2">السعر</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($services as $service)
                        <tr>
                            <td class="py-3 text-slate-700 dark:text-white">{{ $service->name }}</td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">{{ $service->category?->name }}</td>
                            <td class="py-3 text-slate-700 dark:text-white">{{ number_format($service->price, 2) }} USD</td>
                            <td class="py-3">
                                @if ($service->is_active)
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">مفعلة</span>
                                @else
                                    <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">متوقفة</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <a href="{{ route('admin.services.edit', $service) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تعديل</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد خدمات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
