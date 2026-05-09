@extends('layouts.app')

@section('title', 'طلبات الوكالة')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-400">طلبات الوكالة</h1>
                <p class="mt-1 text-sm text-slate-900 dark:text-slate-400">
                    {{ $requests->total() }} طلب وارد
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        @php
            $fieldDefs = \App\Models\AgencyRequestField::orderBy('sort_order')->orderBy('id')->get()->keyBy('name_key');
        @endphp

        <div class="mt-6 space-y-4">
            @forelse ($requests as $request)
                @php
                    $payload = $request->payload ?: array_filter([
                        'contact_number' => $request->contact_number,
                        'full_name' => $request->full_name,
                        'region' => $request->region,
                        'starting_amount' => $request->starting_amount,
                    ], fn ($value) => filled($value));
                @endphp

                <div class="rounded-2xl border border-slate-50 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40 p-5 transition hover:shadow-md">
                    {{-- Header row --}}
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs font-semibold text-emerald-900 dark:text-emerald-300">
                            #{{ $request->id }}
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-900">{{ $request->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    {{-- Fields grid --}}
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($payload as $key => $value)
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-900">
                                    {{ $fieldDefs[$key]->localized_label ?? $key }}
                                </p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-white break-words">
                                    {{ $value }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer action --}}
                    <div class="mt-4 flex justify-start">
                        <a href="{{ route('admin.agency-requests.show', $request) }}"
                           class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300 dark:border-emerald-700 px-4 py-1.5 text-xs font-semibold text-emerald-900 dark:text-emerald-300 transition hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                            عرض التفاصيل &larr;
                        </a>
                    </div>
                </div>

            @empty
                <div class="py-16 text-center text-slate-400 dark:text-slate-900">
                    <p class="text-lg font-medium">لا توجد طلبات بعد.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $requests->links() }}</div>
    </div>
@endsection
