@extends('layouts.app')

@section('title', 'نظام VIP')

@section('content')
    @php
        $currentTier = $summary['current_tier'] ?? null;
        $currentRank = $currentTier?->rank ?? 0;
        $nextTier = $summary['next_tier'] ?? null;
    @endphp

    <x-card :hover="false" class="p-8">
        <x-page-header title="نظام VIP" subtitle="متابعة إجمالي مشترياتك ومستوى VIP الحالي." />

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">إجمالي مشترياتك</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">{{ number_format($summary['spent'] ?? 0, 2) }} USD</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">مستواك الحالي</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">
                    {{ $currentTier?->name ?? 'بدون مستوى' }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">المبلغ المطلوب للوصول للمستوى التالي</p>
                @if ($nextTier)
                    <p class="mt-2 text-2xl font-semibold text-slate-800">{{ number_format($summary['remaining_to_next'] ?? 0, 2) }} USD</p>
                @else
                    <p class="mt-2 text-lg font-semibold text-emerald-700">أنت في أعلى مستوى</p>
                @endif
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-emerald-700">مستويات VIP</h2>
                <span class="text-xs text-slate-500">التقدم نحو المستوى التالي: {{ number_format($summary['progress_percent'] ?? 0, 0) }}%</span>
            </div>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full bg-emerald-500" style="width: {{ min(100, max(0, $summary['progress_percent'] ?? 0)) }}%"></div>
            </div>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tiers as $tier)
                @php
                    $isCurrent = $tier->rank === $currentRank;
                    $isCompleted = $tier->rank < $currentRank;
                    $isLocked = $tier->rank > $currentRank;

                    $cardClasses = 'border-slate-200 bg-white';
                    $stateText = 'مغلق';
                    $stateClass = 'bg-slate-100 text-slate-600';

                    if ($isCompleted) {
                        $cardClasses = 'border-emerald-200 bg-emerald-50/60';
                        $stateText = 'مكتمل';
                        $stateClass = 'bg-emerald-100 text-emerald-700';
                    }

                    if ($isCurrent) {
                        $cardClasses = 'border-emerald-300 bg-emerald-50';
                        $stateText = 'المستوى الحالي';
                        $stateClass = 'bg-emerald-200 text-emerald-800';
                    }
                @endphp
                <div class="rounded-2xl border {{ $cardClasses }} p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white">
                                @if ($tier->badge_image_path)
                                    <img src="{{ asset('storage/'.$tier->badge_image_path) }}" alt="{{ $tier->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-sm font-semibold text-slate-500">VIP</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-slate-800">VIP{{ $tier->rank }}</p>
                                <p class="text-xs text-slate-500">{{ $tier->name }}</p>
                            </div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stateClass }}">{{ $stateText }}</span>
                    </div>
                    <div class="mt-4 text-sm text-slate-600">
                        إجمالي المشتريات المطلوبة: <span class="font-semibold text-slate-800">{{ number_format($tier->threshold_amount, 2) }} USD</span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
@endsection
