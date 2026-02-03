@extends('layouts.app')

@section('title', 'التصنيفات')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">التصنيفات</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">إدارة التصنيفات الرئيسية والفرعية.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة تصنيف</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2">الاسم</th>
                    <th class="py-2">التصنيف الأب</th>
                    <th class="py-2">المعرف</th>
                    <th class="py-2">الحالة</th>
                    <th class="py-2">الترتيب</th>
                    <th class="py-2">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($categories as $category)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $category->name }}</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $category->parent?->name ?? 'تصنيف رئيسي' }}</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $category->slug }}</td>
                        <td class="py-3">
                            @if ($category->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">مفعل</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">متوقف</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $category->sort_order }}</td>
                        <td class="py-3 flex items-center gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تعديل</a>
                            
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-700 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد تصنيفات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
