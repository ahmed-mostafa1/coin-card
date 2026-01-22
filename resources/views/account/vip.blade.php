@extends('layouts.app')

@section('title', __('messages.vip_title'))

@section('content')
    @php
        $currentTier = $summary['current_tier'] ?? null;
        $currentRank = $currentTier?->rank ?? 0;
        $nextTier = $summary['next_tier'] ?? null;
    @endphp

    <x-card :hover="false" class="p-8">
        <x-page-header :title="__('messages.vip_title')" :subtitle="__('messages.vip_desc')" />

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">{{ __('messages.total_spent') }}</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">{{ number_format($summary['spent'] ?? 0, 2) }} USD</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">{{ __('messages.current_level') }}</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">
                    {{ $currentTier?->name ?? __('messages.no_level') }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">{{ __('messages.amount_to_next_level') }}</p>
                @if ($nextTier)
                    <p class="mt-2 text-2xl font-semibold text-slate-800">
                        {{ number_format($summary['remaining_to_next'] ?? 0, 2) }} USD</p>
                @else
                    <p class="mt-2 text-lg font-semibold text-emerald-700">{{ __('messages.highest_level') }}</p>
                @endif
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700">{{ __('messages.vip_levels') }}</h2>
                <span class="text-xs text-slate-500">{{ __('messages.progress_to_next') }}:
                    {{ number_format($summary['progress_percent'] ?? 0, 0) }}%</span>
            </div>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full bg-emerald-500"
                    style="width: {{ min(100, max(0, $summary['progress_percent'] ?? 0)) }}%"></div>
            </div>
        </div>

        @php
            $spentTotal = (float) ($summary['spent'] ?? 0);
            $vipCards = [
                ['rank' => 1, 'name' => 'VIP1', 'threshold' => 500.00, 'image' => 'img/vip1.png'],
                ['rank' => 2, 'name' => 'VIP2', 'threshold' => 1000.00, 'image' => 'img/vip2.png'],
                ['rank' => 3, 'name' => 'VIP3', 'threshold' => 2000.00, 'image' => 'img/vip3.png'],
                ['rank' => 4, 'name' => 'VIP4', 'threshold' => 3500.00, 'image' => 'img/vip4.png'],
                ['rank' => 5, 'name' => 'VIP5', 'threshold' => 5000.00, 'image' => 'img/vip5.png'],
            ];

            $currentVipRank = 0;
            foreach ($vipCards as $card) {
                if ($spentTotal >= $card['threshold']) {
                    $currentVipRank = $card['rank'];
                }
            }
        @endphp

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($vipCards as $card)
                @php
                    $isCurrent = $card['rank'] === $currentVipRank;
                    $isCompleted = $card['rank'] < $currentVipRank;

                    $cardClasses = 'border-slate-200 bg-white';
                    $stateText = __('messages.status_locked');
                    $stateClass = 'bg-slate-100 text-slate-600';

                    if ($isCompleted) {
                        $cardClasses = 'border-emerald-200 bg-emerald-50/60';
                        $stateText = __('messages.status_completed');
                        $stateClass = 'bg-emerald-100 text-emerald-700';
                    }

                    if ($isCurrent) {
                        $cardClasses = 'border-emerald-300 bg-emerald-50';
                        $stateText = __('messages.status_current');
                        $stateClass = 'bg-emerald-200 text-emerald-800';
                    }
                @endphp
                <div class="rounded-2xl border {{ $cardClasses }} p-6 text-center">
                    <div class="mb-4 flex justify-center">
                        <img src="{{ asset($card['image']) }}" alt="{{ $card['name'] }}" class="h-20 w-20 object-contain">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-right">
                            <p class="text-lg font-semibold text-slate-800">{{ $card['name'] }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stateClass }}">{{ $stateText }}</span>
                    </div>
                    <div class="mt-3 text-sm text-slate-600">
                        {{ __('messages.required_spent') }}: <span
                            class="font-semibold text-slate-800">{{ number_format($card['threshold'], 2) }} USD</span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
@endsection