@extends('layouts.app')

@section('title', __('messages.deposit_title'))
@section('mainWidth', 'w-full max-w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400 text-center">{{ __('messages.deposit_title') }}</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 text-center">{{ __('messages.deposit_desc') }}</p>

        <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @forelse ($methods as $method)
                <a href="{{ route('deposit.show', $method->slug) }}" class="group flex flex-col items-center gap-2">
                    {{-- Card Container --}}
                    <div class="relative flex aspect-square w-full flex-col items-center justify-center overflow-hidden rounded-2xl transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-xl group-hover:ring-2 group-hover:ring-emerald-500/20">
                        
                    {{-- Image Container (Ring + Logo) --}}
                        <div class="absolute inset-2 z-10 flex items-center justify-center overflow-hidden rounded-full border-[5px] border-yellow-400 bg-white shadow-[0_0_10px_rgba(250,204,21,0.3)] dark:bg-slate-700">
                            @if ($method->icon_path)
                                <img src="{{ asset('storage/' . $method->icon_path) }}" alt="{{ $method->localized_name }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-slate-100 text-3xl font-bold text-slate-600">
                                    {{ mb_substr($method->localized_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        {{-- Diagonal Stripe Overlay (Subtle) --}}
                        <div class="pointer-events-none absolute inset-0 opacity-20" 
                            style="background-image: linear-gradient(45deg, transparent 45%, #ffffff 50%, transparent 55%); background-size: 200% 200%; opacity: 0.05;">
                        </div>
                    </div>
                    
                    {{-- Method Name --}}
                    <h2 class="text-center text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 sm:text-sm">
                        {{ $method->localized_name }}
                    </h2>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-12 text-center text-sm text-slate-500 dark:text-slate-400">
                    {{ __('messages.no_payment_methods') }}
                </div>
            @endforelse
        </div>
    </div>
@endsection