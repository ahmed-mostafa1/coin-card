@extends('layouts.app')

@section('title', 'حقول التوثيق')

@section('content')
    <x-card :hover="false" class="p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <x-page-header title="حقول التوثيق" subtitle="إدارة الحقول المطلوبة من المستخدم عند طلب التوثيق." />
            <a href="{{ route('admin.verification-fields.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">إضافة حقل</a>
        </div>
        @if (session('status'))<div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>@endif
        <x-table class="mt-6">
            <thead><tr><th class="py-2">الاسم</th><th class="py-2">المفتاح</th><th class="py-2">النوع</th><th class="py-2">الحالة</th><th class="py-2">إجراءات</th></tr></thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($fields as $field)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $field->label }}</td>
                        <td class="py-3 text-slate-500">{{ $field->name_key }}</td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $field->type }}</td>
                        <td class="py-3">{{ $field->is_enabled ? 'مفعل' : 'معطل' }}</td>
                        <td class="py-3"><a href="{{ route('admin.verification-fields.edit', $field) }}" class="text-emerald-700">تعديل</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">لا توجد حقول.</td></tr>
                @endforelse
            </tbody>
        </x-table>
    </x-card>
@endsection
