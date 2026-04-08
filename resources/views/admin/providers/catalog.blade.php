@extends('layouts.app')

@section('title', 'كتالوج ' . $provider->name)
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    @php
        $baseQuery = array_filter([
            'search' => $search !== '' ? $search : null,
            'category' => $categoryFilter !== '' ? $categoryFilter : null,
            'product_type' => $productTypeFilter !== '' ? $productTypeFilter : null,
        ], fn ($value) => $value !== null && $value !== '');

        $pageModeQuery = array_merge($baseQuery, ['mode' => 'page']);
        $allModeQuery = array_merge($baseQuery, ['mode' => 'all']);
        $resetQuery = ['mode' => $mode];
        $previousQuery = array_merge($baseQuery, ['mode' => $mode, 'page' => max(1, $currentPage - 1)]);
        $nextQuery = array_merge($baseQuery, ['mode' => $mode, 'page' => $currentPage + 1]);
    @endphp

    <x-card :hover="false">
        <x-page-header title="كتالوج: {{ $provider->name }}"
                       subtitle="إجمالي النتائج: {{ number_format($count) }}">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.services.index') }}"
                   class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    إدارة الخدمات
                </a>
                <a href="{{ route('admin.providers.index') }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">
                    ← المزودون
                </a>
            </div>
        </x-page-header>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.providers.catalog.index', array_merge(['provider' => $provider], $pageModeQuery)) }}"
               class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium transition {{ $mode === 'page' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                عرض مقسم للصفحات
            </a>
            <a href="{{ route('admin.providers.catalog.index', array_merge(['provider' => $provider], $allModeQuery)) }}"
               class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium transition {{ $mode === 'all' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                {{ $isDailyCard ? 'تحميل الكل' : 'محاولة تحميل الكل' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.providers.catalog.index', $provider) }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4">
            <input type="hidden" name="mode" value="{{ $mode }}">

            <div class="md:col-span-2">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="بحث في اسم المنتج أو الوصف..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
            </div>

            @if($isDailyCard)
                <div>
                    <input type="number"
                           name="category"
                           value="{{ $categoryFilter }}"
                           min="1"
                           placeholder="معرف الفئة"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                           dir="ltr" />
                </div>

                <div>
                    <select name="product_type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">كل الأنواع</option>
                        <option value="package" @selected($productTypeFilter === 'package')>package</option>
                        <option value="quantity" @selected($productTypeFilter === 'quantity')>quantity</option>
                        <option value="stock" @selected($productTypeFilter === 'stock')>stock</option>
                        <option value="topup" @selected($productTypeFilter === 'topup')>topup</option>
                    </select>
                </div>
            @endif

            <div class="md:col-span-4 flex flex-wrap items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    بحث
                </button>
                <a href="{{ route('admin.providers.catalog.index', array_merge(['provider' => $provider], $resetQuery)) }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">
                    إعادة ضبط
                </a>
            </div>
        </form>

        @if($isDailyCard && $searchIsLocal)
            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:bg-slate-700/50 dark:text-slate-400">
                بحث DailyCard يتم محليًا بعد تحميل النتائج، لأن واجهة المزود تعيد أخطاء عند استخدام `search` مباشرة.
            </div>
        @endif

        @if($mode === 'all' && ! $wasTruncated)
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                تم تحميل الكتالوج الكامل في صفحة واحدة.
                @if($isDailyCard)
                    تم استخدام طلب موسّع مناسب لـ DailyCard.
                @endif
            </div>
        @elseif($mode === 'all' && $wasTruncated)
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                تعذر تحميل بقية صفحات الكتالوج من المزود. المعروض حاليًا أول {{ count($products) }} منتج فقط.
                استخدم العرض المقسم للصفحات أو البحث لتضييق النتائج.
            </div>
        @else
            <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700 dark:border-sky-800 dark:bg-sky-900/30 dark:text-sky-400">
                يتم عرض النتائج صفحة بصفحة من المزود.
                الصفحة {{ $currentPage }}
                @if($totalPages > 1)
                    من {{ $totalPages }}.
                @else
                    .
                @endif
            </div>
        @endif

        <x-table class="mt-4">
            <thead class="bg-slate-50 text-slate-500 dark:bg-slate-700/50 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-right">المعرّف</th>
                    <th class="px-3 py-2 text-right">الاسم</th>
                    <th class="px-3 py-2 text-right">النوع</th>
                    <th class="px-3 py-2 text-right">السعر</th>
                    <th class="px-3 py-2 text-right">التوفر</th>
                    <th class="px-3 py-2 text-right">الاستيراد</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($products as $product)
                    @php $extId = $product['external_id']; $isImported = isset($importedIds[$extId]); @endphp
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-3 py-3 font-mono text-xs text-slate-400">{{ $extId }}</td>
                        <td class="px-3 py-3 font-medium text-slate-700 dark:text-white">{{ $product['name'] }}</td>
                        <td class="px-3 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $product['type'] ?? '—' }}</td>
                        <td class="px-3 py-3 font-semibold text-emerald-600">{{ number_format((float) $product['price'], 2) }}</td>
                        <td class="px-3 py-3">
                            @if($product['available'])
                                <x-badge type="done">متاح</x-badge>
                            @else
                                <x-badge type="rejected">غير متاح</x-badge>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            @if($isImported)
                                <x-badge type="processing">مستورَد</x-badge>
                            @else
                                <form method="POST" action="{{ route('admin.providers.catalog.import', $provider) }}">
                                    @csrf
                                    <input type="hidden" name="product_data" value="{{ json_encode($product) }}">
                                    <button type="submit"
                                            class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-medium text-white hover:bg-emerald-700 transition">
                                        استيراد
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400">لا توجد منتجات.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        @if($mode === 'page' && ($hasPreviousPage || $hasNextPage || $totalPages > 1))
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    الصفحة {{ $currentPage }}
                    @if($totalPages > 1)
                        من {{ $totalPages }}
                    @endif
                </p>

                <div class="flex items-center gap-2">
                    @if($hasPreviousPage)
                        <a href="{{ route('admin.providers.catalog.index', array_merge(['provider' => $provider], $previousQuery)) }}"
                           class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">
                            السابق
                        </a>
                    @endif

                    @if($hasNextPage)
                        <a href="{{ route('admin.providers.catalog.index', array_merge(['provider' => $provider], $nextQuery)) }}"
                           class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                            التالي
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </x-card>
@endsection
