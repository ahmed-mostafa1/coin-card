@extends('layouts.app')

@section('title', __('messages.services_page_title'))
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 shadow-sm transition-colors duration-200 sm:p-6 lg:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.services_page_title') }}</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('messages.services_page_desc') }}</p>
            </div>
            <div class="admin-action-bar lg:w-auto">
                <form action="{{ route('admin.services.index') }}" method="GET" class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:w-auto">
                    <div class="relative w-full sm:w-80">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder=".." 
                               class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pr-10 pl-4 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-white dark:focus:border-emerald-500 dark:focus:bg-slate-900">
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="admin-action-button admin-action-button-primary">
                        {{ __('messages.search_button') }}
                    </button>
                </form>
                @if(request('search'))
                    <a href="{{ route('admin.services.index') }}" class="admin-action-button admin-action-button-secondary">
                        إلغاء البحث
                    </a>
                @endif
                <a href="{{ route('admin.services.create') }}" class="admin-action-button admin-action-button-primary">{{ __('messages.add_service') }}</a>
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
                    <th class="py-2">حالة المزود</th>
                    <th class="py-2">{{ __('messages.status') }}</th>
                    <th class="py-2">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($services as $service)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="py-3" data-label="{{ __('messages.name_label') }}">
                            <div class="flex flex-wrap items-center gap-2 text-slate-700 dark:text-white">
                                <span>{{ $service->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400" data-label="{{ __('messages.category') }}">{{ $service->category?->name }}</td>
                        <td class="py-3 text-slate-700 dark:text-white" data-label="{{ __('messages.price') }}">{{ number_format($service->price, 2) }} USD</td>
                        <td class="py-3" data-label="المصدر">
                            @if (empty($service->source) || $service->source === 'manual')
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs text-slate-700 dark:text-slate-300">يدوي</span>
                            @else
                                <span class="rounded-full bg-indigo-100 dark:bg-indigo-900/50 px-3 py-1 text-xs text-indigo-700 dark:text-indigo-400 font-medium">
                                    {{ $service->source === 'dailycard' ? 'DailyCard' : ucfirst($service->source) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-xs text-slate-500 dark:text-slate-400" data-label="حالة المزود">
                            @if ($service->provider_id)
                                <div class="space-y-1">
                                    <span class="rounded-full px-2 py-1 {{ $service->isProviderAvailable() ? 'bg-emerald-50 text-emerald-950' : 'bg-rose-100 text-rose-700' }}">{{ $service->provider_status ?? ($service->provider_is_available ? 'available' : 'unavailable') }}</span>
                                    <div>{{ $service->provider_status_synced_at?->format('Y-m-d H:i') ?? '-' }}</div>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3" data-label="{{ __('messages.status') }}">
                            @if ($service->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">{{ __('messages.status_active') }}</span>
                            @else
                                <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">{{ __('messages.status_inactive') }}</span>
                            @endif
                        </td>
                        <td class="py-3" data-label="{{ __('messages.actions') }}">
                            <div class="admin-inline-actions">
                                <a href="{{ route('admin.services.edit', $service) }}" class="admin-inline-link">{{ __('messages.edit') }}</a>
                             
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="admin-inline-link-danger">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_services_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="mt-4">
            {{ $services->links() }}
        </div>
    </div>
@endsection
