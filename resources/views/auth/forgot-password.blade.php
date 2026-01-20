@extends('layouts.auth')

@section('title', 'استعادة كلمة المرور')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">استعادة كلمة المرور</h1>
            <p class="mt-2 text-sm text-slate-600">أدخل بريدك الإلكتروني لنرسل رابط إعادة التعيين.</p>
        </div>

        <div>
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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
