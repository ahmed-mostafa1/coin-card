@extends('layouts.app')

@section('title', 'مستويات VIP')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">مستويات VIP</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">إدارة مستويات العضوية والمميزات.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ __('messages.return_to_dashboard') }}
                </a>
                <a href="{{ route('admin.vip-tiers.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة مستوى</a>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2">الرتبة</th>
                    <th class="py-2">الشعار</th>
                    <th class="py-2">الاسم (عربي)</th>
                    <th class="py-2">الاسم (إنجليزي)</th>
                    <th class="py-2">المبلغ المطلوب</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($tiers as $tier)
                    <tr>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $tier->rank }}</td>
                        <td class="py-3">
                            @if($tier->image_path)
                                <img src="{{ asset('storage/' . $tier->image_path) }}" alt="{{ $tier->title_ar }}" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <span class="text-sm text-slate-400">لا يوجد</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-700 dark:text-white font-medium">{{ $tier->title_ar }}</td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $tier->title_en }}</td>
                        <td class="py-3 text-emerald-600 dark:text-emerald-400 font-bold">$ {{ number_format($tier->deposits_required, 2) }}</td>
                        <td class="py-3">
                            @if ($tier->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">مفعل</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">متوقف</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.vip-tiers.edit', $tier) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تعديل</a>
                                <form action="{{ route('admin.vip-tiers.destroy', $tier) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد مستويات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
