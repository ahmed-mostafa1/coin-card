@extends('layouts.app')

@section('title', 'تعيين كلمة مرور جديدة')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">تعيين كلمة مرور جديدة</h1>
        <p class="mt-2 text-sm text-slate-600">اختر كلمة مرور جديدة لحسابك.</p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" value="البريد الإلكتروني" />
                <x-text-input id="email" name="email" type="email" :value="old('email', $request->email)" required autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="كلمة المرور الجديدة" />
                <x-text-input id="password" name="password" type="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="تأكيد كلمة المرور" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
            </div>

            <x-primary-button class="w-full">حفظ كلمة المرور</x-primary-button>
        </form>
    </div>
@endsection
