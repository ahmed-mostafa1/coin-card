@extends('layouts.app')

@section('title', __('messages.account_title'))
@section('mainWidth', 'max-w-none')

@section('content')
    @role('admin')
        <div class="w-full lg:min-w-[1000px]">
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
        </div>
    @else
    <div class="w-full lg:min-w-[1000px]">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-8 lg:col-span-2" :hover="false">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ __('messages.account_dashboard_title') }}</h1>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ __('messages.account_dashboard_desc') }}</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-5">
                <a href="{{ route('deposit.index') }}" class="rounded-2xl border border-emerald-100 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-4 text-center text-sm font-semibold text-emerald-700 dark:text-emerald-400 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50">{{ __('messages.top_up') }}</a>
                <a href="{{ route('account.deposits') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.deposit_requests') }}</a>
                <a href="{{ route('account.wallet') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.wallet_history') }}</a>
                <a href="{{ route('account.orders') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.my_orders') }}</a>
                <a href="{{ route('account.vip') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.vip_system') }}</a>
                <a href="{{ route('account.notifications') }}" class="relative rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">
                    {{ __('messages.notifications') }}
                    @if (! empty($unreadNotificationsCount))
                        <span class="absolute left-2 top-2 rounded-full bg-rose-500 px-2 text-xs text-white">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('account.password.change') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-700">{{ __('messages.change_password') }}</a>
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
                                <td class="py-3 text-slate-700 dark:text-slate-300">{{ $order->service->name }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300">{{ $order->variant?->name ?? '-' }}</td>
                                <td class="py-3 text-slate-700 dark:text-slate-300">{{ number_format($order->amount_held, 2) }} USD</td>
                                <td class="py-3">
                                    @if ($order->status === 'new')
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">{{ __('messages.status_new') }}</span>
                                    @elseif ($order->status === 'processing')
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">{{ __('messages.status_processing') }}</span>
                                    @elseif ($order->status === 'done')
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">{{ __('messages.status_done') }}</span>
                                    @else
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">{{ __('messages.status_rejected') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">{{ $order->created_at->format('Y-m-d') }}</td>
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
                                <td class="py-3 text-slate-700 dark:text-slate-300">{{ number_format($deposit->user_amount, 2) }} USD</td>
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
    </div>
    @endrole
@endsection
