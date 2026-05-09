@extends('layouts.app')

@section('title', 'إدارة حقول صفحة طلب الوكالة')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-400">إدارة حقول صفحة طلب الوكالة</h1>
                <p class="mt-2 text-sm text-slate-900 dark:text-slate-50 dark:text-slate-400">تحكم في الحقول التي تظهر في نموذج طلب الوكالة</p>
            </div>
            <a href="{{ route('admin.agency-request-fields.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">إضافة حقل جديد</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-50 dark:border-slate-700">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-50">الترتيب</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-50">العنوان</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-50">المفتاح</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-50">النوع</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-50">مطلوب</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-900 dark:text-slate-50">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse ($fields as $field)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-50">{{ $field->sort_order }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-50">
                                <div>{{ $field->label }}</div>
                                @if ($field->label_en)
                                    <div class="text-xs text-slate-9000 dark:text-slate-50">{{ $field->label_en }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-slate-900 dark:text-slate-50">{{ $field->name_key }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-50">
                                <span class="rounded-full bg-slate-50 dark:bg-slate-700 px-2 py-1 text-xs">{{ $field->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-50">
                                @if ($field->is_required)
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2 py-1 text-xs text-emerald-900 dark:text-emerald-300">نعم</span>
                                @else
                                    <span class="rounded-full bg-slate-50 dark:bg-slate-700 px-2 py-1 text-xs text-slate-900 dark:text-slate-50">لا</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.agency-request-fields.edit', $field) }}" class="text-emerald-900 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">تعديل</a>
                                    <form method="POST" action="{{ route('admin.agency-request-fields.destroy', $field) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحقل؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-900 dark:text-slate-400">
                                لا توجد حقول. <a href="{{ route('admin.agency-request-fields.create') }}" class="text-emerald-900 dark:text-emerald-400 hover:underline">أضف حقل جديد</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.index') }}" class="text-sm text-emerald-900 dark:text-emerald-400 hover:underline">← عودة للوحة الأدمن</a>
        </div>
    </div>
@endsection
