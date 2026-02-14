@extends('layouts.app')

@section('title', __('messages.services_page_title'))
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.services_page_title') }}</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('messages.services_page_desc') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.services.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو ID..." class="rounded-lg border-slate-200 dark:border-slate-700 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-slate-900 dark:text-white">
                    <button type="submit" class="rounded-lg bg-emerald-600 p-2 text-white hover:bg-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.services.index') }}" class="rounded-lg bg-slate-200 p-2 text-slate-600 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
                <a href="{{ route('admin.services.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">{{ __('messages.add_service') }}</a>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <x-table class="mt-6">
            <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="py-2">{{ __('messages.name_label') }}</th>
                    <th class="py-2">{{ __('messages.category') }}</th>
                    <th class="py-2">{{ __('messages.price') }}</th>
                    <th class="py-2">المصدر</th>
                    <th class="py-2">{{ __('messages.status') }}</th>
                    <th class="py-2">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($services as $service)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="py-3 text-slate-700 dark:text-white">{{ $service->name }}</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $service->category?->name }}</td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ number_format($service->price, 2) }} USD</td>
                        <td class="py-3">
                            @if (($service->source ?? 'manual') === 'marketcard99')
                                <div class="flex flex-col gap-1">
                                    <span class="rounded-full bg-sky-100 dark:bg-sky-900/50 px-3 py-1 text-xs text-sky-700 dark:text-sky-300">MarketCard99</span>
                                    @if ($service->external_product_id)
                                        <span class="text-[11px] text-slate-500">ID: {{ $service->external_product_id }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs text-slate-700 dark:text-slate-300">يدوي</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if ($service->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">{{ __('messages.status_active') }}</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">{{ __('messages.status_inactive') }}</span>
                            @endif
                        </td>
                        <td class="py-3 flex items-center gap-3">
                            <a href="{{ route('admin.services.edit', $service) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">{{ __('messages.edit') }}</a>
                            
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-rose-700 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_services_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="mt-4">
            {{ $services->links() }}
        </div>
    </div>
@endsection
