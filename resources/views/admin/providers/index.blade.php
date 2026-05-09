@extends('layouts.app')

@section('title', 'مزودو الـ API')

@section('content')
    <x-card :hover="false">
        <x-page-header title="مزودو الـ API" subtitle="إدارة جميع مزودي الخدمات الخارجية">
            <x-slot name="actions">
                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('admin.providers.sync-statuses') }}" onsubmit="return confirm('تشغيل مزامنة حالة الخدمات لكل المزودين الآن؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 px-4 py-2 text-sm font-medium text-emerald-900 hover:bg-emerald-50 transition">
                            مزامنة الحالات الآن
                        </button>
                    </form>
                    <a href="{{ route('admin.providers.create') }}"
                       class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                        + إضافة مزود جديد
                    </a>
                </div>
            </x-slot>
        </x-page-header>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mt-4 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if($providers->isEmpty())
            <p class="mt-6 text-center text-slate-900 dark:text-slate-400">لا يوجد مزودون بعد. أضف مزوداً لبدء استيراد المنتجات.</p>
        @else
            <x-table class="mt-6">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="py-2 px-3 text-right">الاسم</th>
                        <th class="py-2 px-3 text-right">المعرّف</th>
                        <th class="py-2 px-3 text-right">نوع المصادقة</th>
                        <th class="py-2 px-3 text-right">الخدمات</th>
                        <th class="py-2 px-3 text-right">الحالة</th>
                        <th class="py-2 px-3 text-right">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                    @foreach($providers as $provider)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="py-3 px-3 font-semibold text-slate-700 dark:text-white">{{ $provider->name }}</td>
                            <td class="py-3 px-3">
                                <code class="text-xs bg-slate-50 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $provider->slug }}</code>
                            </td>
                            <td class="py-3 px-3 text-sm text-slate-900 dark:text-slate-400">
                                {{ \App\Models\ApiProvider::AUTH_TYPES[$provider->auth_type] ?? $provider->auth_type }}
                            </td>
                            <td class="py-3 px-3 text-slate-700 dark:text-white">{{ $provider->services_count }}</td>
                            <td class="py-3 px-3">
                                @if($provider->is_active)
                                    <x-badge type="done">مفعّل</x-badge>
                                @else
                                    <x-badge>معطّل</x-badge>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                     <a href="{{ route('admin.providers.catalog.index', $provider) }}"
                                        class="text-sm text-emerald-600 hover:underline">الكتالوج</a>
                                     <form method="POST" action="{{ route('admin.providers.sync-provider-statuses', $provider) }}" onsubmit="return confirm('مزامنة حالة خدمات هذا المزود الآن؟')">
                                         @csrf
                                         <button type="submit" class="text-sm text-amber-600 hover:underline">مزامنة الحالة</button>
                                     </form>
                                     <a href="{{ route('admin.providers.edit', $provider) }}"
                                        class="text-sm text-blue-600 hover:underline">تعديل</a>
                                    <form method="POST" action="{{ route('admin.providers.destroy', $provider) }}"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المزود؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </x-card>
@endsection
