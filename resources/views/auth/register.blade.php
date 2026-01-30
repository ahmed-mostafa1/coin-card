@extends('layouts.auth')

@section('title', __('messages.register'))

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="text-center">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ __('messages.register') }}</h1>
            <p class="mt-3 text-base text-slate-600 dark:text-slate-400">{{ __('messages.register_desc') }}</p>
        </div>

        {{-- Register Form --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name Field --}}
            <div class="space-y-2">
                <x-input-label for="name" :value="__('messages.full_name')" class="text-sm font-semibold text-slate-700 dark:text-slate-300" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus
                    autocomplete="name"
                    class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                    placeholder="{{ __('messages.full_name') }}" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Email Field --}}
            <div class="space-y-2">
                <x-input-label for="email" :value="__('messages.email_label')" class="text-sm font-semibold text-slate-700 dark:text-slate-300" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autocomplete="username"
                    class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                    placeholder="user@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password Field --}}
            <div class="space-y-2">
                <x-input-label for="password" :value="__('messages.password_label')" class="text-sm font-semibold text-slate-700 dark:text-slate-300" />
                <x-text-input id="password" name="password" type="password" required autocomplete="new-password"
                    class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                    placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Confirm Password Field --}}
            <div class="space-y-2">
                <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" class="text-sm font-semibold text-slate-700 dark:text-slate-300" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password"
                    class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                    placeholder="••••••••" />
            </div>

            {{-- Create Account Button --}}
            <x-primary-button class="w-full justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-emerald-500/30 transition hover:from-emerald-700 hover:to-emerald-800 hover:shadow-xl hover:shadow-emerald-500/40 focus:ring-4 focus:ring-emerald-500/20">
                {{ __('messages.create_account_btn') }}
            </x-primary-button>
        </form>

        {{-- Divider --}}
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white dark:bg-slate-800 px-4 text-slate-500 dark:text-slate-400 font-medium">{{ __('messages.or') }}</span>
            </div>
        </div>

        {{-- Google Signup --}}
        <div>
            <a href="{{ route('google.redirect') }}"
                class="flex w-full items-center justify-center gap-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-md">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ __('messages.signup_google') }}
            </a>
        </div>

        {{-- Login Link --}}
        <div class="text-center">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                {{ __('messages.already_have_account') }}
                <a href="{{ route('login') }}"
                    class="font-semibold text-emerald-600 dark:text-emerald-500 transition hover:text-emerald-700 dark:hover:text-emerald-400">
                    {{ __('messages.login') }}
                </a>
            </p>
        </div>
    </div>
@endsection