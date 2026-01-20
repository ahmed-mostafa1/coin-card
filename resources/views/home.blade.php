@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <section class="grid gap-6 md:grid-cols-2">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-semibold text-emerald-700">مرحباً بك في كوين كارد</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                اختر فئة الخدمة المناسبة ثم أتم عملية الشراء مباشرة من رصيد محفظتك.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">ابدأ الآن</a>
                    <a href="{{ route('login') }}" class="rounded-full border border-emerald-200 px-5 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">لدي حساب بالفعل</a>
                @endguest
                @auth
                    <a href="{{ route('account') }}" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">اذهب إلى حسابي</a>
                @endauth
            </div>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-500 to-emerald-700 p-8 text-white shadow-lg">
            <h2 class="text-2xl font-semibold">تسوق خدماتك بسهولة</h2>
            <p class="mt-3 text-sm leading-7 text-emerald-100">
                الطلبات تتم يدوياً ويجري تنفيذها من فريق الإدارة بعد مراجعة البيانات.
            </p>
            <div class="mt-6 grid gap-3 text-sm">
                <div class="rounded-2xl bg-white/10 px-4 py-3">نماذج ديناميكية حسب الخدمة</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3">شراء مباشر من رصيد المحفظة</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3">تتبع حالة الطلب خطوة بخطوة</div>
            </div>
        </div>
    </section>

    <section class="mt-10 rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-emerald-700">الفئات المتاحة</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="group rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-200">
                    @if ($category->image_path)
                        <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="h-32 w-full rounded-xl object-cover">
                    @else
                        <div class="flex h-32 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">{{ mb_substr($category->name, 0, 1) }}</div>
                    @endif
                    <h3 class="mt-4 text-sm font-semibold text-slate-700 group-hover:text-emerald-700">{{ $category->name }}</h3>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
                    لا توجد فئات مفعلة حالياً.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-10 rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-emerald-700">أحدث الخدمات</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($services as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="group rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-200">
                    @if ($service->image_path)
                        <img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name }}" class="h-32 w-full rounded-xl object-cover">
                    @else
                        <div class="flex h-32 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">{{ mb_substr($service->name, 0, 1) }}</div>
                    @endif
                    <h3 class="mt-4 text-sm font-semibold text-slate-700 group-hover:text-emerald-700">{{ $service->name }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $service->category->name }}</p>
                    <p class="mt-2 text-sm font-semibold text-emerald-700">{{ number_format($service->price, 2) }} ر.س</p>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
                    لا توجد خدمات متاحة حالياً.
                </div>
            @endforelse
        </div>
    </section>
@endsection
