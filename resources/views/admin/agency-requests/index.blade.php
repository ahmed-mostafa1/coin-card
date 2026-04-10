@extends('layouts.app')

@section('title', 'طلبات الوكالة')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">طلبات الوكالة</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">متابعة الطلبات الواردة.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        @php
            $fieldDefs = \App\Models\AgencyRequestField::orderBy('sort_order')->orderBy('id')->get()->keyBy('name_key');
        @endphp

        <x-table class="mt-6">
            <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2">البيانات</th>
                    <th class="py-2">التاريخ</th>
                    <th class="py-2">عرض</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($requests as $request)
                    @php $payload = $request->payload ?? []; @endphp
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">
                            @foreach ($payload as $key => $value)
                                <span class="block text-xs text-slate-500">{{ $fieldDefs[$key]->localized_label ?? $key }}:</span>
                                <span class="block text-sm font-medium">{{ $value }}</span>
                            @endforeach
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-3">
                            <a href="{{ route('admin.agency-requests.show', $request) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500 dark:text-slate-400">لا توجد طلبات بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-6">{{ $requests->links() }}</div>
    </div>
@endsection
