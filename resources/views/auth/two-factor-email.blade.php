@extends('layouts.auth')

@section('title', 'التحقق الثنائي')

@section('content')
    <div class="space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-50">التحقق الثنائي</h1>
            <p class="mt-3 text-base text-slate-900 dark:text-slate-400">أدخل الرمز المرسل إلى بريدك الإلكتروني لإكمال تسجيل الدخول.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('two-factor.email.verify') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <x-input-label for="code" value="رمز التحقق" />
                <x-text-input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" dir="ltr" required autofocus />
                <x-input-error :messages="$errors->get('code')" />
            </div>

            <x-primary-button class="w-full justify-center">تأكيد الدخول</x-primary-button>
        </form>

        <form method="POST" action="{{ route('two-factor.email.resend') }}" class="text-center">
            @csrf
            <button type="submit" class="text-sm font-semibold text-emerald-600 hover:text-emerald-900 dark:bg-slate-500 dark:text-emerald-50 dark:hover:bg-slate-50 dark:bg-slate-500 dark:text-emerald-50 dark:hover:bg-slate-50">إرسال رمز جديد</button>
        </form>
    </div>
@endsection
