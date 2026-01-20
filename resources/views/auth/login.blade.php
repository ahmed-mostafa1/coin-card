@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">تسجيل الدخول</h1>
        <p class="mt-2 text-sm text-slate-600">أدخل بياناتك للوصول إلى حسابك.</p>

        <div class="mt-6">
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="البريد الإلكتروني" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="كلمة المرور" />
                <x-text-input id="password" name="password" type="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                تذكرني
            </label>

            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <a href="{{ route('password.request') }}" class="text-emerald-600 transition hover:text-emerald-700">نسيت كلمة المرور؟</a>
                <a href="{{ route('register') }}" class="text-slate-500 transition hover:text-emerald-700">إنشاء حساب جديد</a>
            </div>

            <x-primary-button class="w-full">تسجيل الدخول</x-primary-button>
        </form>

        <div class="mt-6">
            <a href="{{ route('google.redirect') }}" class="flex w-full items-center justify-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                تسجيل الدخول عبر Google
            </a>
        </div>
    </div>
@endsection
