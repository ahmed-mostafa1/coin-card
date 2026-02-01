@extends('layouts.app')

@section('title', 'إدارة النوافذ المنبثقة')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">النوافذ المنبثقة</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">إدارة النوافذ التي تظهر للمستخدمين عند زيارة الموقع.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ __('messages.return_to_dashboard') }}
                </a>
                <a href="{{ route('admin.popups.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة نافذة</a>
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
                    <th class="py-2">العنوان</th>
                    <th class="py-2">الصورة</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">الترتيب</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($popups as $popup)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $popup->title ?: 'بدون عنوان' }}</td>
                        <td class="py-3">
                            @if($popup->image_path)
                                <img src="{{ asset('storage/' . $popup->image_path) }}" alt="{{ $popup->title }}" class="h-16 w-28 rounded-lg object-cover">
                            @else
                                <span class="text-sm text-slate-400">لا توجد صورة</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if ($popup->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">مفعل</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">متوقف</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $popup->display_order }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.popups.edit', $popup) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تعديل</a>
                                <form action="{{ route('admin.popups.destroy', $popup) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد نوافذ منبثقة بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
