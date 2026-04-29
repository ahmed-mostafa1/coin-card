@extends('layouts.app')
@section('title', 'طلبات التوثيق')
@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="طلبات التوثيق" subtitle="مراجعة طلبات توثيق المستخدمين." />
        <x-table class="mt-6">
            <thead><tr><th class="py-2">المستخدم</th><th class="py-2">الحالة</th><th class="py-2">التاريخ</th><th class="py-2">عرض</th></tr></thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($requests as $request)
                    <tr><td class="py-3 text-slate-700 dark:text-white">{{ $request->user?->name }} <x-user-badge :user="$request->user" /></td><td class="py-3">{{ $request->status }}</td><td class="py-3 text-slate-500">{{ $request->created_at->format('Y-m-d H:i') }}</td><td class="py-3"><a href="{{ route('admin.verification-requests.show', $request) }}" class="text-emerald-700">عرض</a></td></tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-slate-500">لا توجد طلبات.</td></tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="mt-6">{{ $requests->links() }}</div>
    </x-card>
@endsection
