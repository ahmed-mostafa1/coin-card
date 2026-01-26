@extends('layouts.app')

@section('title', __('messages.deposit_request_title', ['id' => $depositRequest->id]))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">{{ __('messages.deposit_request_title', ['id' => $depositRequest->id]) }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ __('messages.created_at_label', ['date' => $depositRequest->created_at->format('Y-m-d H:i')]) }}</p>
                </div>
                <a href="{{ route('account.deposits') }}" class="text-sm text-emerald-700 hover:text-emerald-900">{{ __('messages.back_to_deposits') }}</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.payment_method_label') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $depositRequest->paymentMethod->name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.user_amount_label') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format($depositRequest->user_amount, 2) }} USD</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.approved_amount_label') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        {{ $depositRequest->approved_amount ? number_format($depositRequest->approved_amount, 2) : '-' }} USD
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.status') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">
                        @if ($depositRequest->status === 'pending')
                            {{ __('messages.status_pending') }}
                        @elseif ($depositRequest->status === 'approved')
                            {{ __('messages.status_approved') }}
                        @else
                            {{ __('messages.status_rejected') }}
                        @endif
                    </p>
                </div>
            </div>


            @if ($depositRequest->paymentMethod->fields->isNotEmpty())
                <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">{{ __('messages.additional_details_label') }}</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach ($depositRequest->paymentMethod->fields->sortBy('sort_order') as $field)
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs text-slate-500">{{ $field->label }}</p>
                                <p class="text-sm font-semibold text-slate-700">{{ ($depositRequest->payload ?? [])[$field->name_key] ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($depositRequest->status === 'rejected' && $depositRequest->admin_note)
                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    {{ __('messages.rejection_reason_label', ['reason' => $depositRequest->admin_note]) }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">{{ __('messages.admin_notes_title') }}</h2>
            <p class="mt-3 text-sm text-slate-600">{{ __('messages.admin_notes_desc') }}</p>
        </div>
    </div>
@endsection
