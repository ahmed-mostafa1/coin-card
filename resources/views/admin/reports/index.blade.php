@extends('layouts.app')

@section('title', 'تقارير الإدارة')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">تقارير الإدارة</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">ملخص النشاط خلال الفترة المحددة.</p>
                </div>
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-3 text-sm">
                    <div class="flex items-center gap-2">
                        <label for="from" class="text-slate-600 dark:text-slate-300">من</label>
                        <input type="date" id="from" name="from" value="{{ $from }}" class="rounded-full border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-700 dark:text-white" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="to" class="text-slate-600 dark:text-slate-300">إلى</label>
                        <input type="date" id="to" name="to" value="{{ $to }}" class="rounded-full border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-700 dark:text-white" />
                    </div>
                    <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">تطبيق</button>
                </form>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                <a href="{{ route('admin.reports.index', ['preset' => 'today']) }}"
                    class="rounded-full border px-3 py-1 transition {{ $preset === 'today' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">اليوم</a>
                <a href="{{ route('admin.reports.index', ['preset' => '7']) }}"
                    class="rounded-full border px-3 py-1 transition {{ $preset === '7' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">آخر 7 أيام</a>
                <a href="{{ route('admin.reports.index', ['preset' => '30']) }}"
                    class="rounded-full border px-3 py-1 transition {{ $preset === '30' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">آخر 30 يوم</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">طلبات الشحن</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">إجمالي الطلبات</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $depositTotal }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">معتمدة</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $depositCounts['approved'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">مرفوضة</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $depositCounts['rejected'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">قيد المراجعة</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $depositCounts['pending'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">إجمالي المعتمد</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ number_format($depositApprovedSum, 2) }} USD</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">الطلبات</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">إجمالي الطلبات</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $orderTotal }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">جديد</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $orderCounts['new'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">قيد التنفيذ</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $orderCounts['processing'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">تم التنفيذ</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $orderCounts['done'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">مرفوض</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ $orderCounts['rejected'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">إيراد محصل</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ number_format($settledRevenue, 2) }} USD</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">لقطة المحفظة</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">الرصيد المتاح</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ number_format($totalBalance, 2) }} USD</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">الرصيد المعلّق</span>
                        <span class="font-semibold text-slate-700 dark:text-white">{{ number_format($totalHeld, 2) }} USD</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h3 class="text-base font-semibold text-emerald-700 dark:text-emerald-400">أكثر الخدمات طلبًا</h3>
                <div class="mt-4 space-y-3 text-sm">
                    @forelse ($topServices as $service)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 dark:text-slate-300">{{ $service->name }}</span>
                            <span class="font-semibold text-slate-700 dark:text-white">{{ $service->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد بيانات كافية للفترة.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h3 class="text-base font-semibold text-emerald-700 dark:text-emerald-400">أعلى المستخدمين حسب التحصيل</h3>
                <div class="mt-4 space-y-3 text-sm">
                    @forelse ($topUsers as $user)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 dark:text-slate-300">{{ $user->name }}<span class="text-xs text-slate-400 dark:text-slate-500"> ({{ $user->email }})</span></span>
                            <span class="font-semibold text-slate-700 dark:text-white">{{ number_format($user->total, 2) }} USD</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد بيانات كافية للفترة.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
