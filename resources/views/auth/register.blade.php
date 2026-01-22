@extends('layouts.auth')

@section('title', __('messages.register'))

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('messages.register') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('messages.register_desc') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="__('messages.full_name')" />
                <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus
                    autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('messages.email_label')" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" :value="__('messages.password_label')" />
                <x-text-input id="password" name="password" type="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password" />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <a href="{{ route('login') }}"
                    class="text-slate-500 transition hover:text-emerald-700">{{ __('messages.already_have_account') }}</a>
                <x-primary-button>{{ __('messages.create_account_btn') }}</x-primary-button>
            </div>
        </form>
    </div>
@endsection