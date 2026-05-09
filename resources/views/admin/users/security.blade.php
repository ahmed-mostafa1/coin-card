@extends('layouts.app')

@section('title', 'سجلات الأمان')

@section('content')
    <div class="space-y-6">
        <x-card :hover="false" class="p-8">
            <x-page-header title="سجلات الأمان: {{ $user->name }}" subtitle="كل النشاط التقني والأمني المرتبط بالمستخدم.">
                <a href="{{ route('admin.users.show', $user) }}" class="rounded-xl border border-slate-50 px-4 py-2 text-sm font-semibold text-slate-900">عودة للمستخدم</a>
            </x-page-header>

            <form method="GET" class="mt-6 grid gap-3 md:grid-cols-4">
                <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                <select name="action" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                    <option value="">كل الإجراءات</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
                <input type="text" name="ip" value="{{ request('ip') }}" placeholder="IP" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                <input type="text" name="country" value="{{ request('country') }}" placeholder="Country code" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                <select name="device" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                    <option value="">كل الأجهزة</option>
                    @foreach (['desktop','mobile','tablet','bot'] as $device)
                        <option value="{{ $device }}" @selected(request('device') === $device)>{{ $device }}</option>
                    @endforeach
                </select>
                <input type="number" name="order_id" value="{{ request('order_id') }}" placeholder="رقم الطلب" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                <input type="number" name="deposit_request_id" value="{{ request('deposit_request_id') }}" placeholder="رقم الشحن" class="rounded-xl border border-slate-50 px-3 py-2 text-sm">
                <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">فلترة</button>
            </form>
        </x-card>

        <x-card :hover="false" class="p-6">
            <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">السجلات</h2>
            <x-table class="mt-4">
                <thead><tr><th>التاريخ</th><th>الإجراء</th><th>IP</th><th>البلد</th><th>الجهاز</th><th>المتصفح</th><th>مرجع</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="py-2 text-sm">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-2 text-sm font-semibold">{{ $log->action }}</td>
                            <td class="py-2 text-sm">{{ $log->ip_address ?? '-' }}</td>
                            <td class="py-2 text-sm">{{ $log->country_code ?? '-' }}</td>
                            <td class="py-2 text-sm">{{ $log->device_type ?? '-' }}</td>
                            <td class="py-2 text-sm">{{ $log->browser ?? '-' }}</td>
                            <td class="py-2 text-sm">{{ $log->order_id ? 'Order #'.$log->order_id : ($log->deposit_request_id ? 'Deposit #'.$log->deposit_request_id : '-') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-900">لا توجد سجلات.</td></tr>
                    @endforelse
                </tbody>
            </x-table>
            <div class="mt-4">{{ $logs->links() }}</div>
        </x-card>

        <div class="grid gap-6 lg:grid-cols-3">
            <x-card :hover="false" class="p-6">
                <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">سجل IP</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @forelse ($ipHistories as $ip)
                        <div class="rounded-xl border border-slate-50 p-3">{{ $ip->ip_address }} - {{ $ip->country_code ?? '-' }} - {{ $ip->seen_count }} مرة</div>
                    @empty
                        <p class="text-slate-900">لا توجد بيانات.</p>
                    @endforelse
                </div>
            </x-card>
            <x-card :hover="false" class="p-6">
                <h2 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100">محاولات الدخول الفاشلة</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @forelse ($failedAttempts as $attempt)
                        <div class="rounded-xl border border-slate-50 p-3">{{ $attempt->created_at->format('Y-m-d H:i') }} - {{ $attempt->ip_address ?? '-' }} - {{ $attempt->reason }}</div>
                    @empty
                        <p class="text-slate-900">لا توجد محاولات.</p>
                    @endforelse
                </div>
            </x-card>
            <x-card :hover="false" class="p-6">
                <h2 class="text-lg font-semibold text-rose-700">تنبيهات الاشتباه</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @forelse ($suspiciousLogs as $alert)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-rose-800">{{ $alert->title }} - {{ $alert->severity }}</div>
                    @empty
                        <p class="text-slate-900">لا توجد تنبيهات.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
@endsection
