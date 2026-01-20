@extends('layouts.app')

@section('title', 'إنشاء حساب')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">إنشاء حساب</h1>
        <p class="mt-2 text-sm text-slate-600">ابدأ رحلتك معنا ببيانات بسيطة.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="name" value="الاسم الكامل" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="البريد الإلكتروني" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="كلمة المرور" />
                <x-text-input id="password" name="password" type="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="تأكيد كلمة المرور" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <a href="{{ route('login') }}" class="text-slate-500 transition hover:text-emerald-700">لدي حساب بالفعل</a>
                <x-primary-button>إنشاء الحساب</x-primary-button>
            </div>
        </form>
    </div>
@endsection
