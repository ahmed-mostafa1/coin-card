@extends('layouts.app')

@section('title', 'كتالوج ' . $provider->name)
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <x-card :hover="false">
        <x-page-header title="كتالوج: {{ $provider->name }}"
                       subtitle="إجمالي المنتجات: {{ number_format($count) }}">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.index') }}"
                   class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    إدارة الخدمات
                </a>
                <a href="{{ route('admin.providers.index') }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    ← المزودون
                </a>
            </div>
        </x-page-header>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mt-4 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif
        <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
            تم عرض الكتالوج كاملاً في صفحة واحدة.
        </div>
        @if($wasTruncated)
            <div class="mt-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/30 px-4 py-3 text-sm text-amber-700 dark:text-amber-400">
                تم عرض أول {{ count($products) }} منتج فقط. راجع العرض العادي للصفحات إذا احتجت بقية الكتالوج.
            </div>
        @endif

        <div class="mt-4 mb-4">
            <input type="text" id="product-search" placeholder="بحث في المنتجات..."
                   class="w-full md:w-80 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                   oninput="filterProducts(this.value)" />
        </div>

        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2 px-3 text-right">المعرّف</th>
                    <th class="py-2 px-3 text-right">الاسم</th>
                    <th class="py-2 px-3 text-right">النوع</th>
                    <th class="py-2 px-3 text-right">السعر</th>
                    <th class="py-2 px-3 text-right">التوفر</th>
                    <th class="py-2 px-3 text-right">الاستيراد</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($products as $product)
                    @php $extId = $product['external_id']; $isImported = isset($importedIds[$extId]); @endphp
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50 product-row"
                        data-name="{{ strtolower($product['name']) }}"
                        data-type="{{ strtolower($product['type'] ?? '') }}">
                        <td class="py-3 px-3 text-xs text-slate-400 font-mono">{{ $extId }}</td>
                        <td class="py-3 px-3 font-medium text-slate-700 dark:text-white">{{ $product['name'] }}</td>
                        <td class="py-3 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $product['type'] ?? '—' }}</td>
                        <td class="py-3 px-3 font-semibold text-emerald-600">{{ number_format((float) $product['price'], 2) }}</td>
                        <td class="py-3 px-3">
                            @if($product['available'])
                                <x-badge type="done">متاح</x-badge>
                            @else
                                <x-badge type="rejected">غير متاح</x-badge>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            @if($isImported)
                                <x-badge type="processing">مستورد</x-badge>
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
    </x-card>
@endsection

@push('scripts')
<script>
    function filterProducts(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.product-row').forEach(row => {
            const name = row.dataset.name ?? '';
            const type = row.dataset.type ?? '';
            row.style.display = (name.includes(q) || type.includes(q)) ? '' : 'none';
        });
    }
</script>
@endpush
