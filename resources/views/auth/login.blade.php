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

        <div>
            <a href="{{ route('google.redirect') }}"
                class="flex w-full items-center justify-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                {{ __('messages.login_google') }}
            </a>
        </div>
    </div>
@endsection