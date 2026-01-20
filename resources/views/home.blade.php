@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <section class="grid gap-6 md:grid-cols-2">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-semibold text-emerald-700">مرحباً بك في كوين كارد</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                هذه منصة بطاقات رقمية بأساسيات جاهزة للانطلاق. يمكنك إنشاء حساب والبدء في إدارة حسابك بأمان.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">ابدأ الآن</a>
                    <a href="{{ route('login') }}" class="rounded-full border border-emerald-200 px-5 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">لدي حساب بالفعل</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">اذهب إلى لوحة التحكم</a>
                @endauth
            </div>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-500 to-emerald-700 p-8 text-white shadow-lg">
            <h2 class="text-2xl font-semibold">جاهزون للمرحلة القادمة</h2>
            <p class="mt-3 text-sm leading-7 text-emerald-100">
                تم تجهيز تسجيل الدخول، الصلاحيات، وواجهة عربية كاملة باتجاه من اليمين لليسار.
            </p>
            <div class="mt-6 grid gap-3 text-sm">
                <div class="rounded-2xl bg-white/10 px-4 py-3">تسجيل حسابات آمن مع إعادة تعيين كلمة المرور</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3">دخول عبر Google للحسابات السريعة</div>
                <div class="rounded-2xl bg-white/10 px-4 py-3">صلاحيات أساسية للأدمن والعملاء</div>
            </div>
        </div>
    </section>
@endsection
