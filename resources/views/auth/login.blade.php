@extends('layouts.auth')

@section('title', __('messages.login'))

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('messages.login') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('messages.login_desc') }}</p>
        </div>

        <div>
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('messages.email_label')" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" :value="__('messages.password_label')" />
                <x-text-input id="password" name="password" type="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember"
                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                {{ __('messages.remember_me') }}
            </label>

            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <a href="{{ route('password.request') }}"
                    class="text-emerald-600 transition hover:text-emerald-700">{{ __('messages.forgot_password') }}</a>
                <a href="{{ route('register') }}"
                    class="text-slate-500 transition hover:text-emerald-700">{{ __('messages.create_new_account') }}</a>
            </div>

            <x-primary-button class="w-full">{{ __('messages.login') }}</x-primary-button>
        </form>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-4 text-slate-500">{{ __('messages.or') }}</span>
            </div>
        </div>

        <div>
            <a href="{{ route('google.redirect') }}"
                class="flex w-full items-center justify-center gap-3 rounded-full border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:border-slate-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ __('messages.login_google') }}
            </a>
        </div>
    </div>
@endsection