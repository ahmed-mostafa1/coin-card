@extends('layouts.auth')

@section('title', __('messages.forgot_password_title'))

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('messages.forgot_password_title') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('messages.forgot_password_desc') }}</p>
        </div>

        <div>
            <x-auth-session-status :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('messages.email_label')" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <x-primary-button class="w-full">{{ __('messages.send_reset_link') }}</x-primary-button>
        </form>
    </div>
@endsection