@extends('layouts.app')

@section('title', __('messages.my_orders_title'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <x-card :hover="false">
        <x-page-header :title="__('messages.my_orders_title')" :subtitle="__('messages.my_orders_desc')" />

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="py-2">{{ __('messages.service') }}</th>
                    <th class="py-2">{{ __('messages.package') }}</th>
                    <th class="py-2">{{ __('messages.price_label') }}</th>
                    <th class="py-2">{{ __('messages.held_amount') }}</th>
                    <th class="py-2">{{ __('messages.status') }}</th>
                    <th class="py-2">{{ __('messages.date') }}</th>
                    <th class="py-2">{{ __('messages.details_link') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    <tr class="transition hover:bg-slate-50">
                        <td class="py-3 text-slate-700">{{ $order->service->name }}</td>
                        <td class="py-3 text-slate-700">{{ $order->variant?->name ?? '-' }}</td>
                        <td class="py-3 text-slate-700">
                            @if ($order->discount_percentage > 0)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400 line-through">{{ number_format($order->original_price, 2) }}</span>
                                    <span class="font-semibold text-emerald-700">{{ number_format($order->price_at_purchase, 2) }}</span>
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-1.5 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                        -{{ number_format($order->discount_percentage, 0) }}%
                                    </span>
                                </div>
                            @else
                                {{ number_format($order->price_at_purchase, 2) }} USD
                            @endif
                        </td>
                        <td class="py-3 text-slate-700">{{ number_format($order->amount_held, 2) }} USD</td>
                        <td class="py-3">
                            @if ($order->status === 'new')
                                <x-badge type="new">{{ __('messages.status_new') }}</x-badge>
                            @elseif ($order->status === 'processing')
                                <x-badge type="processing">{{ __('messages.status_processing') }}</x-badge>
                            @elseif ($order->status === 'done')
                                <x-badge type="done">{{ __('messages.status_done') }}</x-badge>
                            @elseif ($order->status === 'rejected')
                                <x-badge type="rejected">{{ __('messages.status_rejected') }}</x-badge>
                            @else
                                <x-badge>{{ __('messages.status_cancelled') }}</x-badge>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500">{{ $order->created_at->format('Y-m-d') }}</td>
                        <td class="py-3">
                            <a href="{{ route('account.orders.show', $order) }}"
                                class="text-emerald-700 hover:text-emerald-900">{{ __('messages.view_link') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-500">{{ __('messages.no_orders_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-6">{{ $orders->links() }}</div>
    </x-card>
@endsection