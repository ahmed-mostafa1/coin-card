@extends('layouts.app')

@section('title', 'استعادة كلمة المرور')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">استعادة كلمة المرور</h1>
        <p class="mt-2 text-sm text-slate-600">أدخل بريدك الإلكتروني لنرسل رابط إعادة التعيين.</p>

        <div class="mt-6">
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="البريد الإلكتروني" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <x-primary-button class="w-full">إرسال رابط الاستعادة</x-primary-button>
        </form>
    </div>
@endsection
