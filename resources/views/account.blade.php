@extends('layouts.app')

@section('title', __('messages.account_title'))
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    @php
        $accountExtraLinks = array_values(array_filter([
            [
                'url' => route('account.security'),
                'label' => app()->getLocale() == 'ar' ? 'الأمان والتحقق الثنائي' : 'Security and 2FA',
                'icon' => 'fa-solid fa-user-shield',
                'external' => false,
            ],
            [
                'url' => route('contact-us.show'),
                'label' => __('messages.contact_page'),
                'icon' => 'fa-solid fa-envelope',
                'external' => false,
            ],
            [
                'url' => route('about'),
                'label' => __('messages.about_us'),
                'icon' => 'fa-solid fa-circle-info',
                'external' => false,
            ],
            [
                'url' => route('privacy-policy'),
                'label' => __('messages.privacy_policy'),
                'icon' => 'fa-solid fa-shield-halved',
                'external' => false,
            ],
            [
                'url' => route('terms-of-use'),
                'label' => __('messages.terms_of_use'),
                'icon' => 'fa-solid fa-file-contract',
                'external' => false,
            ],
            [
                'url' => route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar'),
                'label' => app()->getLocale() == 'ar' ? 'English' : 'العربية',
                'icon' => 'fa-solid fa-language',
                'external' => false,
            ],
            [
                'url' => $sharedWhatsappLink ?? null,
                'label' => __('messages.contact_whatsapp'),
                'icon' => 'fa-brands fa-whatsapp',
                'external' => true,
            ],
        ], fn ($link) => filled($link['url'])));
    @endphp

    @role('admin')
        <div class="w-full lg:min-w-[1000px]">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr),minmax(320px,1fr)]">
            <x-card :hover="false" class="w-full p-8">
            <x-page-header :title="__('messages.account_update_title')" :subtitle="__('messages.account_update_desc')" />

            @if (session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('account.update') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('messages.name_label')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', auth()->user()->name)" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('messages.email_label')" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', auth()->user()->email)" required />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('messages.new_password')" />
                    <x-text-input id="password" name="password" type="password" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                </div>

                <x-primary-button class="w-full">{{ __('messages.save_changes') }}</x-primary-button>
            </form>
            </x-card>
            <x-card :hover="false" class="p-8">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">روابط إضافية</h2>
                <p class="mt-2 text-sm text-slate-900 dark:text-slate-50 dark:text-slate-400">الروابط الثانوية نُقلت هنا للحفاظ على شريط علوي أخف وأوضح.</p>
                <div class="mt-6 grid gap-3">
                    @foreach ($accountExtraLinks as $link)
                        <a href="{{ $link['url'] }}"
                           @if($link['external']) target="_blank" rel="noreferrer noopener" @endif
                           class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">
                            <span class="flex items-center gap-3">
                                <i class="{{ $link['icon'] }} {{ $link['external'] ? 'text-green-500' : 'text-emerald-600 dark:text-emerald-400' }}"></i>
                                <span>{{ $link['label'] }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-left text-xs text-slate-400 rtl:rotate-180"></i>
                        </a>
                    @endforeach

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center justify-between rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300 dark:hover:border-rose-800 dark:hover:bg-rose-950/50">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-arrow-right-from-bracket rtl:rotate-180"></i>
                                <span>{{ __('messages.logout') }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-left text-xs rtl:rotate-180"></i>
                        </button>
                    </form>
                </div>
            </x-card>
            </div>
        </div>
    @else
    <div class="w-full lg:min-w-[1000px]">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-8 lg:col-span-2" :hover="false">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ __('messages.account_dashboard_title') }}</h1>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ __('messages.account_dashboard_desc') }}</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-5">
                <a href="{{ route('deposit.index') }}" class="rounded-2xl border border-emerald-100 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-4 text-center text-sm font-semibold text-emerald-700 dark:text-emerald-400 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">{{ __('messages.top_up') }}</a>
                <a href="{{ route('account.deposits') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.deposit_requests') }}</a>
                <a href="{{ route('account.wallet') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.wallet_history') }}</a>
                <a href="{{ route('account.orders') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.my_orders') }}</a>
                <a href="{{ route('account.verification.show') }}" class="rounded-2xl border border-emerald-100 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-4 text-center text-sm font-semibold text-emerald-700 dark:text-emerald-400 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">{{ app()->getLocale() === 'ar' ? 'توثيق الحساب' : 'Account verification' }}</a>
                <a href="{{ route('account.security') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ app()->getLocale() === 'ar' ? 'الأمان والتحقق الثنائي' : 'Security and 2FA' }}</a>
                <a href="{{ route('account.notifications') }}" class="relative rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">
                    {{ __('messages.notifications') }}
                    @if (! empty($unreadNotificationsCount))
                        <span class="absolute left-2 top-2 rounded-full bg-rose-500 px-2 text-xs text-white">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('account.password.change') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.change_password') }}</a>
            </div>
        </x-card>
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-600 to-emerald-700 p-8 text-white shadow-sm cc-hover-glow">
            <h2 class="text-lg font-semibold">{{ __('messages.wallet_balance') }}</h2>
            <p class="mt-4 text-3xl font-semibold">{{ number_format($wallet->balance, 2) }} USD</p>
            <p class="mt-2 text-sm text-emerald-100">{{ __('messages.available_balance') }}</p>
            <p class="mt-4 text-xl font-semibold">{{ number_format($wallet->held_balance, 2) }} USD</p>
            <p class="mt-2 text-sm text-emerald-100">{{ __('messages.held_balance') }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-card class="p-8" :hover="false">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.latest_orders') }}</h2>
                <a href="{{ route('account.orders') }}" class="text-sm text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">{{ __('messages.view_all') }}</a>
            </div>
            <x-table class="mt-4">
                    <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="py-2">{{ __('messages.service') }}</th>
                            <th class="py-2">{{ __('messages.package') }}</th>
                            <th class="py-2">{{ __('messages.held_amount') }}</th>
                            <th class="py-2">{{ __('messages.status') }}</th>
                            <th class="py-2">{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="{{ __('messages.service') }}">{{ $order->service->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="{{ __('messages.package') }}">{{ $order->variant?->name ?? '-' }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300" data-label="{{ __('messages.held_amount') }}">{{ number_format($order->amount_held, 2) }} USD</td>
                                <td class="py-3" data-label="{{ __('messages.status') }}">
                                    @if ($order->status === 'new')
                                        <span class="rounded-full bg-amber-100 dark:bg-amber-900/30 px-3 py-1 text-xs text-amber-700 dark:text-amber-300">{{ __('messages.status_new') }}</span>
                                    @elseif ($order->status === 'processing')
                                        <span class="rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-xs text-blue-700 dark:text-blue-300">{{ __('messages.status_processing') }}</span>
                                    @elseif ($order->status === 'done')
                                        <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-300">{{ __('messages.status_done') }}</span>
                                    @else
                                        <span class="rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-xs text-rose-700 dark:text-rose-300">{{ __('messages.status_rejected') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400" data-label="{{ __('messages.date') }}">{{ $order->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_orders_yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
        </x-card>

        <x-card class="p-8" :hover="false">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.latest_deposits') }}</h2>
                <a href="{{ route('account.deposits') }}" class="text-sm text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">{{ __('messages.view_all') }}</a>
            </div>
            <x-table class="mt-4">
                    <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="py-2">{{ __('messages.method') }}</th>
                            <th class="py-2">{{ __('messages.amount') }}</th>
                            <th class="py-2">{{ __('messages.status') }}</th>
                            <th class="py-2">{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($recentDeposits as $deposit)
                            <tr>
                                <td class="py-3 text-slate-700 dark:text-slate-300">{{ $deposit->paymentMethod->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300">
                                    {{ number_format($deposit->net_usd_amount ?? $deposit->user_amount, 2) }} USD
                                    @if ($deposit->currency_code)
                                        <div class="text-xs text-slate-9000 dark:text-slate-50">{{ number_format($deposit->local_amount, 2) }} {{ $deposit->currency_code }}</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if ($deposit->status === 'pending')
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">{{ __('messages.status_pending') }}</span>
                                    @elseif ($deposit->status === 'approved')
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">{{ __('messages.status_approved') }}</span>
                                    @else
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">{{ __('messages.status_rejected') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $deposit->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_deposits_yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
        </x-card>
    </div>

    <x-card class="mt-8 p-8" :hover="false">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">روابط إضافية</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">نقلنا الروابط الثانوية من الشريط العلوي إلى هنا حتى تبقى الواجهة أوضح على الجوال.</p>
            </div>
        </div>
        <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($accountExtraLinks as $link)
                <a href="{{ $link['url'] }}"
                   @if($link['external']) target="_blank" rel="noreferrer noopener" @endif
                   class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-sm font-semibold text-slate-900 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">
                    <span class="flex items-center gap-3">
                        <i class="{{ $link['icon'] }} {{ $link['external'] ? 'text-green-500' : 'text-emerald-600 dark:text-emerald-400' }}"></i>
                        <span>{{ $link['label'] }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-left text-xs text-slate-400 rtl:rotate-180"></i>
                </a>
            @endforeach

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center justify-between rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300 dark:hover:border-rose-800 dark:hover:bg-rose-950/50">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-arrow-right-from-bracket rtl:rotate-180"></i>
                        <span>{{ __('messages.logout') }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-left text-xs rtl:rotate-180"></i>
                </button>
            </form>
        </div>
    </x-card>
    </div>
    @endrole
@endsection
