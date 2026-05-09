@extends('layouts.app')
@section('title', 'طلبات التوثيق')
@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="طلبات التوثيق" subtitle="مراجعة طلبات توثيق المستخدمين." />
        <x-table class="mt-6">
            <thead><tr><th class="py-2">المستخدم</th><th class="py-2">الحالة</th><th class="py-2">التاريخ</th><th class="py-2">عرض</th></tr></thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                @forelse ($requests as $request)
                    <tr>
                        <td class="py-3 text-slate-700 dark:text-white">{{ $request->user?->name }} <x-user-badge :user="$request->user" /></td>
                        <td class="py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-300' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-900 dark:text-slate-400">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-3"><a href="{{ route('admin.verification-requests.show', $request) }}" class="inline-flex rounded-lg border border-emerald-200 dark:border-emerald-700 px-2.5 py-1 text-xs font-semibold text-emerald-900 dark:text-emerald-300">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-slate-900">لا توجد طلبات.</td></tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="mt-6">{{ $requests->links() }}</div>
    </x-card>
@endsection
