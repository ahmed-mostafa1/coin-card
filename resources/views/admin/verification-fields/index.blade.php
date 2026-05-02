@extends('layouts.app')

@section('title', 'حقول التوثيق')

@section('content')
    <x-card :hover="false" class="p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <x-page-header title="حقول التوثيق" subtitle="إدارة الحقول المطلوبة من المستخدم عند طلب التوثيق." />
            <a href="{{ route('admin.verification-fields.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">إضافة حقل</a>
        </div>
        @if (session('status'))<div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>@endif
        <x-table class="mt-6">
            <thead><tr><th class="py-2">الاسم</th><th class="py-2">المفتاح</th><th class="py-2">النوع</th><th class="py-2">الحالة</th><th class="py-2">إجراءات</th></tr></thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($fields as $field)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $field->label }}</td>
                        <td class="py-3 text-slate-500">{{ $field->name_key }}</td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $field->type }}</td>
                        <td class="py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $field->is_enabled ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' }}">
                                {{ $field->is_enabled ? 'مفعل' : 'معطل' }}
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.verification-fields.edit', $field) }}" class="rounded-lg border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-900 dark:border-emerald-700 dark:text-emerald-300">تعديل</a>
                                <form method="POST" action="{{ route('admin.verification-fields.update', $field) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="label" value="{{ $field->label }}">
                                    <input type="hidden" name="label_en" value="{{ $field->label_en }}">
                                    <input type="hidden" name="name_key" value="{{ $field->name_key }}">
                                    <input type="hidden" name="type" value="{{ $field->type }}">
                                    <input type="hidden" name="placeholder" value="{{ $field->placeholder }}">
                                    <input type="hidden" name="placeholder_en" value="{{ $field->placeholder_en }}">
                                    <input type="hidden" name="sort_order" value="{{ $field->sort_order }}">
                                    <input type="hidden" name="options_text" value="{{ is_array($field->options) ? implode(PHP_EOL, $field->options) : '' }}">
                                    <input type="hidden" name="is_required" value="{{ $field->is_required ? 1 : 0 }}">
                                    <input type="hidden" name="is_enabled" value="{{ $field->is_enabled ? 0 : 1 }}">
                                    <button type="submit" class="rounded-lg border border-amber-200 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:border-amber-700 dark:text-amber-300">
                                        {{ $field->is_enabled ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.verification-fields.destroy', $field) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحقل؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:border-rose-700 dark:text-rose-300">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">لا توجد حقول.</td></tr>
                @endforelse
            </tbody>
        </x-table>
    </x-card>
@endsection
