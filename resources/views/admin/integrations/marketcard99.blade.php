@extends('layouts.app')

@section('title', 'تكامل MarketCard99')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">تكامل MarketCard99</h1>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                مزامنة الكتالوج الخارجي ومزامنة حالات الطلبات يدوياً.
            </p>

            @if (session('status'))
                <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mt-4 rounded-lg border border-rose-200 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <form method="POST" action="{{ route('admin.integrations.marketcard99.sync-catalog') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        مزامنة الكتالوج الآن
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.integrations.marketcard99.sync-order-statuses') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-slate-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        مزامنة حالات الطلبات الآن
                    </button>
                </form>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">آخر مزامنة كتالوج</h2>
                @php
                    $catalog = session('catalog_result', $catalogSummary);
                @endphp
                @if ($catalog)
                    <div class="mt-4 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                        <p>بدأت: {{ $catalog['started_at'] ?? '-' }}</p>
                        <p>انتهت: {{ $catalog['finished_at'] ?? '-' }}</p>
                        <p>تصنيفات جديدة: {{ $catalog['categories_created'] ?? 0 }}</p>
                        <p>تصنيفات محدثة: {{ $catalog['categories_updated'] ?? 0 }}</p>
                        <p>خدمات جديدة: {{ $catalog['services_created'] ?? 0 }}</p>
                        <p>خدمات محدثة: {{ $catalog['services_updated'] ?? 0 }}</p>
                        <p>تصنيفات معطلة: {{ $catalog['categories_deactivated'] ?? 0 }}</p>
                        <p>خدمات معطلة: {{ $catalog['services_deactivated'] ?? 0 }}</p>
                    </div>

                    @if (!empty($catalog['errors']))
                        <div class="mt-4 rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 p-4 text-xs text-amber-700 dark:text-amber-300">
                            <p class="font-semibold">ملاحظات/أخطاء:</p>
                            <ul class="mt-2 list-disc pr-5 space-y-1">
                                @foreach (array_slice($catalog['errors'], 0, 15) as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">لا يوجد سجل مزامنة بعد.</p>
                @endif
            </div>

            <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">آخر مزامنة حالات الطلبات</h2>
                @php
                    $orders = session('orders_result', $orderSummary);
                @endphp
                @if ($orders)
                    <div class="mt-4 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                        <p>بدأت: {{ $orders['started_at'] ?? '-' }}</p>
                        <p>انتهت: {{ $orders['finished_at'] ?? '-' }}</p>
                        <p>طلبات مرشحة: {{ $orders['total_candidates'] ?? 0 }}</p>
                        <p>محدّثة: {{ $orders['synced'] ?? 0 }}</p>
                        <p>بدون تغيير: {{ $orders['unchanged'] ?? 0 }}</p>
                        <p>فاشلة: {{ $orders['failed'] ?? 0 }}</p>
                    </div>

                    @if (!empty($orders['errors']))
                        <div class="mt-4 rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 p-4 text-xs text-amber-700 dark:text-amber-300">
                            <p class="font-semibold">ملاحظات/أخطاء:</p>
                            <ul class="mt-2 list-disc pr-5 space-y-1">
                                @foreach (array_slice($orders['errors'], 0, 15) as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">لا يوجد سجل مزامنة بعد.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

