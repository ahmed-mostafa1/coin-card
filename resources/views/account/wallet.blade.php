@extends('layouts.app')

@section('title', __('messages.wallet_history_title'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <x-card :hover="false">
        <x-page-header :title="__('messages.wallet_history_title')" :subtitle="__('messages.wallet_history_desc')">
            <x-slot name="actions">
                <a href="{{ route('deposit.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 dark:text-white transition cc-press">{{ __('messages.top_up') }}</a>
            </x-slot>
        </x-page-header>

        <x-table class="mt-6">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2">{{ __('messages.type') }}</th>
                    <th class="py-2">{{ __('messages.amount') }}</th>
                    <th class="py-2">{{ __('messages.status') }}</th>
                    <th class="py-2">{{ __('messages.date') }}</th>
                    <th class="py-2">{{ __('messages.notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @php
                    $typeLabels = [
                        'deposit' => __('messages.type_deposit'),
                        'hold' => __('messages.type_hold'),
                        'settle' => __('messages.type_settle'),
                        'release' => __('messages.type_release'),
                        'purchase' => __('messages.type_purchase'),
                    ];
                @endphp
                @forelse ($transactions as $transaction)
                    @php
                        $amount = (float) $transaction->amount;
                        $displayAmount = $amount;

                        if ($amount >= 0 && in_array($transaction->type, ['hold', 'settle', 'purchase'], true)) {
                            $displayAmount = -$amount;
                        }

                        $amountClass = $displayAmount >= 0 ? 'text-emerald-700' : 'text-rose-700';
                    @endphp
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-transparent">
                        <td class="py-3 text-slate-900 dark:text-slate-300" data-label="{{ __('messages.type') }}">
                            {{ $typeLabels[$transaction->type] ?? $transaction->type }}
                        </td>
                        <td class="py-3" data-label="{{ __('messages.amount') }}">
                            <span class="{{ $amountClass }}">
                                {{ number_format($displayAmount, 2) }} USD
                            </span>
                        </td>
                        <td class="py-3" data-label="{{ __('messages.status') }}">
                            @if ($transaction->status === 'approved')
                                <x-badge type="approved">{{ __('messages.status_approved_badge') }}</x-badge>
                            @elseif ($transaction->status === 'pending')
                                <x-badge type="pending">{{ __('messages.status_pending_badge') }}</x-badge>
                            @else
                                <x-badge type="rejected">{{ __('messages.status_rejected') }}</x-badge>
                            @endif
                        </td>
                        <td class="py-3 text-slate-700 dark:text-slate-400" data-label="{{ __('messages.date') }}">{{ $transaction->created_at->format('Y-m-d') }}</td>
                        <td class="py-3 text-slate-700 dark:text-slate-400" data-label="{{ __('messages.notes') }}">{{ $transaction->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_transactions_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-6">{{ $transactions->links() }}</div>
    </x-card>
@endsection