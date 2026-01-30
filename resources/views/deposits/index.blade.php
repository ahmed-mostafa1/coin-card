@extends('layouts.app')

@section('title', __('messages.deposit_title'))
@section('mainWidth', 'w-full max-w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">{{ __('messages.deposit_title') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('messages.deposit_desc') }}</p>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse ($methods as $method)
                <a href="{{ route('deposit.show', $method->slug) }}"
                    class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-200">
                    @if ($method->icon_path)
                        <img src="{{ asset('storage/' . $method->icon_path) }}" alt="{{ $method->name }}"
                            class="h-12 w-12 rounded-xl object-cover">
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            {{ mb_substr($method->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-sm font-semibold text-slate-700">{{ $method->name }}</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.deposit_instruction_hint') }}</p>
                    </div>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
                    {{ __('messages.no_payment_methods') }}
                </div>
            @endforelse
        </div>
    </div>
@endsection