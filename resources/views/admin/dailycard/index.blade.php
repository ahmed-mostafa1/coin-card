@extends('layouts.app')

@section('title', 'استيراد منتجات DailyCard')

@section('content')
<div class="w-full px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <x-page-header title="استيراد منتجات DailyCard" subtitle="تصفح الكتالوج واستورد المنتجات المطلوبة" />
        <a href="{{ route('admin.services.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition">
            <i class="fa-solid fa-list-ul text-xs"></i>
            إدارة الخدمات
        </a>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <x-card class="mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            {{-- Client-side search (avoids DailyCard backend bug with LIKE queries) --}}
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">بحث (فلترة فورية)</label>
                <x-text-input id="dc-search" type="text" placeholder="اسم المنتج..." class="w-full" autocomplete="off" />
            </div>
            {{-- Product type is a safe server-side filter (no LIKE query) --}}
            <form method="GET" action="{{ route('admin.dailycard.index') }}" class="flex gap-2 items-end">
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع المنتج</label>
                    <select name="product_type" class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">الكل</option>
                        <option value="package"  @selected(request('product_type') === 'package')>Package</option>
                        <option value="quantity"  @selected(request('product_type') === 'quantity')>Quantity</option>
                        <option value="stock"     @selected(request('product_type') === 'stock')>Stock</option>
                        <option value="topup"     @selected(request('product_type') === 'topup')>Top-Up</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">فلترة</button>
                @if(request('product_type'))
                    <a href="{{ route('admin.dailycard.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">مسح</a>
                @endif
            </form>
        </div>
    </x-card>

    {{-- Results Stats --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
            إجمالي في هذه الصفحة: <strong id="dc-visible-count">{{ count($products) }}</strong>
            (الكل: <strong>{{ number_format($count) }}</strong> | مستورد: <strong>{{ count($importedIds) }}</strong>)
        </p>
        @if($hasNext)
            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800">يوجد صفحات إضافية</span>
        @endif
    </div>

    {{-- Products Grid --}}
    @forelse ($products as $product)
        @php
            $isImported = isset($importedIds[$product['id']]);
            $localServiceId = $isImported ? $importedIds[$product['id']] : null;
            $available = $product['available'] ?? true;
            $price = number_format((float) ($product['price'] ?? 0), 2);
            $type = $product['product_type'] ?? '—';
            $minQty = $product['qty_values']['min'] ?? null;
            $maxQty = $product['qty_values']['max'] ?? null;
        @endphp

        <x-card class="mb-3 flex items-center gap-4" data-product-name="{{ strtolower($product['name']) }}">
            {{-- Product info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-semibold text-gray-800 text-sm">{{ $product['name'] }}</span>
                    <span class="text-xs text-gray-400">#{{ $product['id'] }}</span>
                    @if($isImported)
                        <span class="inline-flex items-center gap-1 text-[11px] bg-emerald-100 text-emerald-900 border border-emerald-200 px-2 py-0.5 rounded-full font-medium">
                            ✓ مستورد
                        </span>
                    @endif
                    @if(!$available)
                        <span class="text-[11px] bg-red-100 text-red-600 border border-red-200 px-2 py-0.5 rounded-full">غير متاح</span>
                    @endif
                </div>
                <div class="flex items-center gap-4 mt-1 text-xs text-gray-500">
                    <span>النوع: <strong>{{ $type }}</strong></span>
                    <span>السعر: <strong class="text-emerald-900">{{ $price }}</strong></span>
                    @if($minQty !== null || $maxQty !== null)
                        <span>الكمية: {{ $minQty ?? '—' }} &ndash; {{ $maxQty ?? '∞' }}</span>
                    @endif
                </div>
            </div>

            {{-- Action --}}
            <div class="shrink-0">
                @if($isImported)
                    <a href="{{ route('admin.services.edit', $localServiceId) }}"
                       class="inline-block px-4 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        تعديل الخدمة
                    </a>
                @else
                    <form method="POST" action="{{ route('admin.dailycard.import') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="name" value="{{ $product['name'] }}">
                        <input type="hidden" name="price" value="{{ $product['price'] ?? 0 }}">
                        <input type="hidden" name="product_type" value="{{ $product['product_type'] ?? '' }}">
                        <input type="hidden" name="available" value="{{ $available ? '1' : '0' }}">
                        @if($minQty !== null)<input type="hidden" name="min_qty" value="{{ $minQty }}">@endif
                        @if($maxQty !== null)<input type="hidden" name="max_qty" value="{{ $maxQty }}">@endif
                        <button type="submit"
                                onclick="return confirm('استيراد {{ addslashes($product['name']) }}؟')"
                                class="px-4 py-1.5 text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                            استيراد
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
    @empty
        <x-empty-state title="لا توجد منتجات" description="لم يتم العثور على منتجات بهذه المعايير." />
    @endforelse

    {{-- Pagination hint --}}
    @if($hasNext)
        <div class="mt-4 flex justify-center">
            <a href="{{ request()->fullUrlWithQuery(['page' => (int)request('page', 1) + 1]) }}"
               class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                الصفحة التالية
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const input   = document.getElementById('dc-search');
        const counter = document.getElementById('dc-visible-count');
        const cards   = document.querySelectorAll('[data-product-name]');

        if (!input) return;

        input.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            let visible = 0;

            cards.forEach(function (card) {
                const name = card.dataset.productName.toLowerCase();
                const show = q === '' || name.includes(q);
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (counter) counter.textContent = visible;
        });
    })();
</script>
@endpush
@endsection
