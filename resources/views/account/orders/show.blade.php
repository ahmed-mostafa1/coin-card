@extends('layouts.app')

@section('title', __('messages.order_details_title'))

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-8 lg:col-span-2" :hover="false">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ __('messages.order_id_title', ['id' => $order->id]) }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ $order->service->name }}</p>
                </div>
                <a href="{{ route('account.orders') }}" class="text-sm text-emerald-700 hover:text-emerald-900">{{ __('messages.back_to_orders') }}</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.price_label') }}</p>
                    @if ($order->discount_percentage > 0)
                        <div class="mt-2 space-y-1">
                            <p class="text-xs text-slate-400 line-through">{{ number_format($order->original_price, 2) }} USD</p>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-emerald-700">{{ number_format($order->price_at_purchase, 2) }} USD</p>
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                    -{{ number_format($order->discount_percentage, 0) }}%
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($order->price_at_purchase, 2) }} USD</p>
                    @endif
                </div>
                @if ($order->discount_percentage > 0)
                    <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-4">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ app()->getLocale() == 'ar' ? 'المبلغ الموفر' : 'Amount Saved' }}</p>
                        <p class="mt-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format($order->discount_amount, 2) }} USD</p>
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">🎉 {{ app()->getLocale() == 'ar' ? 'خصم VIP' : 'VIP Discount' }}</p>
                    </div>
                @endif
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.held_amount') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($order->amount_held, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.status') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
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
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.package') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->variant?->name ?? __('messages.base_price_label') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.date') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.settled_at_label') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->settled_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.released_at_label') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $order->released_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ __('messages.order_data_label') }}</p>
                @if (count($order->payload))
                    <div class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach ($order->payload as $key => $value)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">{{ $fieldLabels[$key] ?? $key }}</span>
                                <span class="font-semibold">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">{{ __('messages.no_additional_data') }}</p>
                @endif
            </div>

            @if ($order->admin_note)
                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ __('messages.admin_note_label', ['note' => $order->admin_note]) }}
                </div>
            @endif
        </x-card>

        <x-card class="p-8" :hover="false">
            <h2 class="text-lg font-semibold text-emerald-700">{{ __('messages.order_history_title') }}</h2>
            @php
                $statusLabels = [
                    'new' => __('messages.status_new'),
                    'processing' => __('messages.status_processing'),
                    'done' => __('messages.status_done'),
                    'rejected' => __('messages.status_rejected'),
                    'cancelled' => __('messages.status_cancelled'),
                ];
            @endphp
            <div class="mt-4 space-y-4">
                @forelse ($order->events as $event)
                    @php
                        $actorLabel = __('messages.actor_system');
                        if ($event->actor) {
                            $actorLabel = $event->actor->id === auth()->id() ? __('messages.actor_you') : __('messages.actor_admin');
                        }
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-700">{{ $event->message ?? __('messages.update_label') }}</p>
                            <span class="text-xs text-slate-400">{{ $event->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ __('messages.actor_label', ['actor' => $actorLabel]) }}</p>
                        @if ($event->old_status || $event->new_status)
                            <p class="mt-1 text-xs text-slate-500">
                                {{ __('messages.status_change_label', [
                                    'old' => $statusLabels[$event->old_status] ?? '-',
                                    'new' => $statusLabels[$event->new_status] ?? '-'
                                ]) }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('messages.no_updates_yet') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>
@endsection
