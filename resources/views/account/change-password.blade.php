@extends('layouts.app')

@section('title', 'تعديل الملف الشخصي')

@section('content')
    <div class="w-full lg:min-w-[600px] lg:max-w-2xl">
        <x-card :hover="false" class="w-full p-8">
            <x-page-header title="تعديل الملف الشخصي" subtitle="تحديث الاسم وكلمة المرور الخاصة بك." />

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
                    <x-input-label for="name" value="الاسم" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', auth()->user()->name)" required
                        autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('messages.email_label')" />
                    <x-text-input id="email" name="email" type="email" dir="ltr" :value="old('email', auth()->user()->email)" required
                        autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">اترك حقول كلمة المرور فارغة إذا كنت لا تريد تغييرها</p>
                    
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="current_password" value="كلمة المرور الحالية" />
                            <x-text-input id="current_password" name="current_password" type="password"
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('current_password')" />
                        </div>

                        <div>
                            <x-input-label for="password" value="كلمة المرور الجديدة" />
                            <x-text-input id="password" name="password" type="password" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="تأكيد كلمة المرور الجديدة" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <x-primary-button class="flex-1">حفظ التغييرات</x-primary-button>
                    <a href="{{ route('account') }}"
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:border-emerald-200">
                        إلغاء
                    </a>
                </div>
            </form>
        </x-card>
    </div>
@endsection
