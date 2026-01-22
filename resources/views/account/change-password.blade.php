@extends('layouts.app')

@section('title', 'تغيير كلمة المرور')

@section('content')
    <div class="w-full lg:min-w-[600px] lg:max-w-2xl">
        <x-card :hover="false" class="w-full p-8">
            <x-page-header title="تغيير كلمة المرور" subtitle="تحديث كلمة المرور الخاصة بك." />

            @if (session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('account.password.update') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="current_password" value="كلمة المرور الحالية" />
                    <x-text-input id="current_password" name="current_password" type="password" required
                        autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('current_password')" />
                </div>

                <div>
                    <x-input-label for="password" value="كلمة المرور الجديدة" />
                    <x-text-input id="password" name="password" type="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="تأكيد كلمة المرور الجديدة" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" required
                        autocomplete="new-password" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button class="flex-1">تحديث كلمة المرور</x-primary-button>
                    <a href="{{ route('account') }}"
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:border-emerald-200">
                        إلغاء
                    </a>
                </div>
            </form>
        </x-card>
    </div>
@endsection