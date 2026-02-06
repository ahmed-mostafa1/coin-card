@extends('layouts.app')

@section('title', __('messages.vip_title'))

@section('content')
    @php
        $currentTier = $summary['current_tier'] ?? null;
        $nextTier = $summary['next_tier'] ?? null;
    @endphp

    <x-card :hover="false" class="p-8">
        <x-page-header :title="__('messages.vip_title')" :subtitle="__('messages.vip_desc')" />

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('messages.total_spent') }}</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800 dark:text-white">{{ number_format($summary['spent'] ?? 0, 2) }} USD</p>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('messages.current_level') }}</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-400">
                    {{ $currentTier?->localized_title ?? __('messages.no_level') }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('messages.amount_to_next_level') }}</p>
                @if ($nextTier)
                    <p class="mt-2 text-2xl font-semibold text-slate-800 dark:text-white">
                        {{ number_format($summary['remaining_to_next'] ?? 0, 2) }} USD</p>
                @else
                    <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.highest_level') }}</p>
                @endif
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.vip_levels') }}</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ __('messages.progress_to_next') }}:
                    {{ number_format($summary['progress_percent'] ?? 0, 0) }}%</span>
            </div>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                <div class="h-full bg-emerald-500"
                    style="width: {{ min(100, max(0, $summary['progress_percent'] ?? 0)) }}%"></div>
            </div>
        </div>

        @php
            $currentVipRank = $currentTier?->rank ?? 0;
        @endphp

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($tiers as $tier)
                @php
                    $isCurrent = $currentTier && $tier->id === $currentTier->id;
                    $isCompleted = $currentVipRank && $tier->rank < $currentVipRank;

                    $cardClasses = 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800';
                    $stateText = __('messages.status_locked');
                    $stateClass = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';

                    if ($isCompleted) {
                        $cardClasses = 'border-emerald-200 dark:border-emerald-700 bg-emerald-50/60 dark:bg-emerald-900/30';
                        $stateText = __('messages.status_completed');
                        $stateClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200';
                    }

                    if ($isCurrent) {
                        $cardClasses = 'border-emerald-300 dark:border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40';
                        $stateText = __('messages.status_current');
                        $stateClass = 'bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100';
                    }
                @endphp
                <div class="rounded-2xl border {{ $cardClasses }} p-6 text-center">
                    <div class="mb-4 flex justify-center">
                        @if ($tier->image_path)
                            <img src="{{ asset('storage/' . $tier->image_path) }}" alt="{{ $tier->title_ar }}" class="h-20 w-20 object-contain">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-sm font-semibold text-slate-500 dark:text-slate-200">VIP</div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-right">
                            <p class="text-lg font-semibold text-slate-800 dark:text-white">{{ $tier->localized_title }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stateClass }}">{{ $stateText }}</span>
                    </div>
                    <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                        <div>
                            {{ __('messages.required_spent') }}: <span
                                class="font-semibold text-slate-800 dark:text-white">{{ number_format($tier->deposits_required, 2) }} USD</span>
                        </div>
                        @if ($tier->discount_percentage > 0)
                            <div class="text-emerald-700 dark:text-emerald-400">
                                {{ __('messages.discount') ?? (app()->getLocale() == 'ar' ? 'خصم' : 'Discount') }}: <span class="font-semibold">{{ number_format($tier->discount_percentage, 0) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 text-center text-sm text-slate-500 dark:text-slate-400">
                    {{ __('messages.no_vip_levels') }}
                </div>
            @endforelse
        </div>
    </x-card>
@endsection
