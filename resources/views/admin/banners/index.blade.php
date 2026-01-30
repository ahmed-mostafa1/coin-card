@extends('layouts.app')

@section('title', 'البانرات')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">البانرات</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">إدارة صور السلايدر الرئيسي.</p>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة بانر</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2">الصورة</th>
                    <th class="py-2">العنوان</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">الترتيب</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($banners as $banner)
                    <tr>
                        <td class="py-3">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="h-16 w-28 rounded-lg object-cover">
                        </td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $banner->title ?: 'بدون عنوان' }}</td>
                        <td class="py-3">
                            @if ($banner->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">مفعل</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">متوقف</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $banner->sort_order }}</td>
                        <td class="py-3">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تعديل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد بانرات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
