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
            <div class="flex flex-wrap items-center gap-4">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder=".." 
                               class="w-80 rounded-xl border border-slate-200 bg-slate-50 py-2.5 pr-10 pl-4 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-white dark:focus:border-emerald-500 dark:focus:bg-slate-900">
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        {{ __('messages.search_button') }}
                    </button>
                </form>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-rose-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-rose-400 transition-colors">
                        إلغاء البحث
                    </a>
                @endif
                <a href="{{ route('admin.categories.create') }}" class="rounded-full bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-emerald-300 dark:shadow-none">
                    إضافة تصنيف
                </a>
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
                    <th class="py-2">الاسم</th>
                    <th class="py-2">التصنيف الأب</th>
                    <th class="py-2">المعرف</th>
                    <th class="py-2">المصدر</th>
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
                            @if (($category->source ?? 'manual') === 'marketcard99')
                                <span class="rounded-full bg-sky-100 dark:bg-sky-900/50 px-3 py-1 text-xs text-sky-700 dark:text-sky-300">MarketCard99</span>
                            @else
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs text-slate-700 dark:text-slate-300">يدوي</span>
                            @endif
                        </td>
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
                            
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-rose-700 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد تصنيفات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
