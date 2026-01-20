@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <section class="grid gap-6 lg:grid-cols-2">
        <x-card class="p-10">
            <h1 class="text-3xl font-bold text-slate-900">منصة احترافية لشحن الخدمات الرقمية</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                اختر الخدمة المناسبة، ادفع من رصيد محفظتك، وتابع الحالة بخطوات واضحة حتى اكتمال التنفيذ.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#categories" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition duration-200 hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cc-press">تصفح الخدمات</a>
                <a href="{{ route('deposit.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition duration-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cc-press">شحن الرصيد</a>
            </div>
        </x-card>
        <x-card class="bg-emerald-600 text-white" :hover="false">
            <h2 class="text-2xl font-semibold">تجربة موثوقة وسريعة</h2>
            <p class="mt-3 text-sm leading-7 text-emerald-100">
                نراجع كل طلب يدويًا لضمان الدقة، مع إشعارات فورية وتحديثات واضحة لكل عميل.
            </p>
            <div class="mt-6 grid gap-3 text-sm">
                <div class="rounded-xl bg-white/10 px-4 py-3">نماذج ديناميكية حسب الخدمة</div>
                <div class="rounded-xl bg-white/10 px-4 py-3">خصم من الرصيد المتاح فقط</div>
                <div class="rounded-xl bg-white/10 px-4 py-3">سجل طلبات ودفعات واضح</div>
            </div>
        </x-card>
    </section>

    <x-card class="mt-10" :hover="false" id="categories">
        <x-page-header title="الفئات المتاحة" subtitle="اختر الفئة المناسبة للبدء." />
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="group rounded-2xl border border-slate-200 p-4 cc-hover-lift">
                    <div class="overflow-hidden rounded-xl">
                        @if ($category->image_path)
                            <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="h-32 w-full object-cover transition duration-200 group-hover:scale-[1.02]">
                        @else
                            <div class="flex h-32 items-center justify-center bg-emerald-50 text-emerald-700">{{ mb_substr($category->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700">{{ $category->name }}</h3>
                        <span class="text-xs text-slate-400 transition group-hover:translate-x-0.5">&larr;</span>
                    </div>
                </a>
            @empty
                <x-empty-state message="لا توجد فئات مفعلة حالياً." />
            @endforelse
        </div>
    </x-card>

    <x-card class="mt-10" :hover="false">
        <x-page-header title="خدمات مميزة" subtitle="اكتشف أحدث الخدمات المتاحة لدينا." />
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($services as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="group rounded-2xl border border-slate-200 p-4 cc-hover-lift">
                    <div class="overflow-hidden rounded-xl">
                        @if ($service->image_path)
                            <img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name }}" class="h-32 w-full object-cover transition duration-200 group-hover:scale-[1.02]">
                        @else
                            <div class="flex h-32 items-center justify-center bg-emerald-50 text-emerald-700">{{ mb_substr($service->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-slate-800 group-hover:text-emerald-700">{{ $service->name }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $service->category->name }}</p>
                    <p class="mt-3 text-sm font-semibold text-emerald-700">
                        @if ($service->variants->count())
                            يبدأ من {{ number_format($service->variants->min('price'), 2) }} USD
                        @else
                            {{ number_format($service->price, 2) }} USD
                        @endif
                    </p>
                </a>
            @empty
                <x-empty-state message="لا توجد خدمات متاحة حالياً." />
            @endforelse
        </div>
    </x-card>
@endsection
