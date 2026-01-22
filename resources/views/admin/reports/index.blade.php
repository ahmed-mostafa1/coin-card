@extends('layouts.app')

@section('title', 'تقارير الإدارة')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">تقارير الإدارة</h1>
                    <p class="mt-2 text-sm text-slate-600">ملخص النشاط خلال الفترة المحددة.</p>
                </div>
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-3 text-sm">
                    <div class="flex items-center gap-2">
                        <label for="from" class="text-slate-600">من</label>
                        <input type="date" id="from" name="from" value="{{ $from }}" class="rounded-full border border-slate-200 px-3 py-2 text-sm text-slate-700" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="to" class="text-slate-600">إلى</label>
                        <input type="date" id="to" name="to" value="{{ $to }}" class="rounded-full border border-slate-200 px-3 py-2 text-sm text-slate-700" />
                    </div>
                    <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">تطبيق</button>
                </form>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                <a href="{{ route('admin.reports.index', ['preset' => 'today']) }}"
                    class="rounded-full border px-3 py-1 transition {{ $preset === 'today' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">اليوم</a>
                <a href="{{ route('admin.reports.index', ['preset' => '7']) }}"
                    class="rounded-full border px-3 py-1 transition {{ $preset === '7' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">آخر 7 أيام</a>
                <a href="{{ route('admin.reports.index', ['preset' => '30']) }}"
                    class="rounded-full border px-3 py-1 transition {{ $preset === '30' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-200' }}">آخر 30 يوم</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700">طلبات الشحن</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">إجمالي الطلبات</span>
                        <span class="font-semibold text-slate-700">{{ $depositTotal }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">معتمدة</span>
                        <span class="font-semibold text-slate-700">{{ $depositCounts['approved'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">مرفوضة</span>
                        <span class="font-semibold text-slate-700">{{ $depositCounts['rejected'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">قيد المراجعة</span>
                        <span class="font-semibold text-slate-700">{{ $depositCounts['pending'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">إجمالي المعتمد</span>
                        <span class="font-semibold text-slate-700">{{ number_format($depositApprovedSum, 2) }} USD</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700">الطلبات</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">إجمالي الطلبات</span>
                        <span class="font-semibold text-slate-700">{{ $orderTotal }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">جديد</span>
                        <span class="font-semibold text-slate-700">{{ $orderCounts['new'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">قيد التنفيذ</span>
                        <span class="font-semibold text-slate-700">{{ $orderCounts['processing'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">تم التنفيذ</span>
                        <span class="font-semibold text-slate-700">{{ $orderCounts['done'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">مرفوض</span>
                        <span class="font-semibold text-slate-700">{{ $orderCounts['rejected'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">إيراد محصل</span>
                        <span class="font-semibold text-slate-700">{{ number_format($settledRevenue, 2) }} USD</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700">لقطة المحفظة</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">الرصيد المتاح</span>
                        <span class="font-semibold text-slate-700">{{ number_format($totalBalance, 2) }} USD</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">الرصيد المعلّق</span>
                        <span class="font-semibold text-slate-700">{{ number_format($totalHeld, 2) }} USD</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-emerald-700">أكثر الخدمات طلبًا</h3>
                <div class="mt-4 space-y-3 text-sm">
                    @forelse ($topServices as $service)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">{{ $service->name }}</span>
                            <span class="font-semibold text-slate-700">{{ $service->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">لا توجد بيانات كافية للفترة.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-emerald-700">أعلى المستخدمين حسب التحصيل</h3>
                <div class="mt-4 space-y-3 text-sm">
                    @forelse ($topUsers as $user)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">{{ $user->name }}<span class="text-xs text-slate-400"> ({{ $user->email }})</span></span>
                            <span class="font-semibold text-slate-700">{{ number_format($user->total, 2) }} USD</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">لا توجد بيانات كافية للفترة.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
