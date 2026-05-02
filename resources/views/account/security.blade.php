@extends('layouts.app')

@section('title', 'الأمان')

@section('content')
    <div class="space-y-6">
        <x-card :hover="false" class="p-8">
            <x-page-header title="الأمان" subtitle="إدارة التحقق الثنائي ومراجعة آخر نشاط أمني." />

            @if (session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-300">{{ session('status') }}</div>
            @endif

            <div class="mt-6 rounded-2xl border border-slate-200 p-5 dark:border-slate-700">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">التحقق الثنائي عبر البريد</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">سيتم إرسال رمز إلى بريدك الإلكتروني بعد كلمة المرور عند تسجيل الدخول.</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ auth()->user()->two_factor_email_enabled ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-50' }}">
                        {{ auth()->user()->two_factor_email_enabled ? 'مفعّل' : 'معطّل' }}
                    </span>
                </div>

                @if (! auth()->user()->two_factor_email_enabled)
                    <form method="POST" action="{{ route('account.security.2fa.enable') }}" class="mt-4 max-w-md space-y-3">
                        @csrf
                        <x-input-label for="enable_current_password" value="كلمة المرور الحالية" />
                        <x-text-input id="enable_current_password" name="current_password" type="password" required />
                        <x-input-error :messages="$errors->get('current_password')" />
                        <x-primary-button>تفعيل التحقق الثنائي</x-primary-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('account.security.2fa.disable') }}" class="mt-4 max-w-md space-y-3">
                        @csrf
                        <x-input-label for="disable_current_password" value="كلمة المرور الحالية" />
                        <x-text-input id="disable_current_password" name="current_password" type="password" required />
                        <x-input-error :messages="$errors->get('current_password')" />
                        <button type="submit" class="rounded-xl border border-rose-200 dark:border-rose-700 px-4 py-2 text-sm font-semibold text-rose-700 dark:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/20">تعطيل التحقق الثنائي</button>
                    </form>
                @endif
            </div>
        </x-card>

        <x-card :hover="false" class="p-8">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">آخر النشاط الأمني</h2>
            <div class="mt-4 space-y-3">
                @forelse ($recentLogs as $log)
                    <div class="rounded-xl border border-slate-200 p-3 text-sm dark:border-slate-700">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-semibold text-slate-700 dark:text-slate-50">{{ $log->action }}</span>
                            <span class="text-xs text-slate-400">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-900 dark:text-slate-50">IP: {{ $log->ip_address ?? '-' }} | {{ $log->device_type ?? '-' }} | {{ $log->browser ?? '-' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد سجلات بعد.</p>
                @endforelse
            </div>
        </x-card>
    </div>
@endsection
