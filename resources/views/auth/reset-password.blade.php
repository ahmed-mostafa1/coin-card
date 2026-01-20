@extends('layouts.auth')

@section('title', 'تعيين كلمة مرور جديدة')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">تعيين كلمة مرور جديدة</h1>
            <p class="mt-2 text-sm text-slate-600">اختر كلمة مرور جديدة لحسابك.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
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
