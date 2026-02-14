@extends('layouts.app')

@section('title', __('messages.service_variants'))

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.service_variants_title', ['name' => $service->name]) }}</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('messages.manage_service_variants_desc') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.services.edit', $service) }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">{{ __('messages.back_to_service') }}</a>
                <a href="{{ route('admin.services.variants.create', $service) }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">{{ __('messages.add_variant') }}</a>
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
                    <th class="py-2">{{ __('messages.price') }}</th>
                    <th class="py-2">{{ __('messages.status') }}</th>
                    <th class="py-2">{{ __('messages.sort_order') }}</th>
                    <th class="py-2">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($variants as $variant)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="py-3 text-slate-700 dark:text-white">{{ $variant->name }}</td>
                        <td class="py-3 text-slate-700 dark:text-white">{{ number_format($variant->price, 2) }} USD</td>
                        <td class="py-3">
                            @if ($variant->is_active)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">{{ __('messages.status_active') }}</span>
                            @else
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs text-slate-700 dark:text-slate-300">{{ __('messages.status_inactive') }}</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">{{ $variant->sort_order }}</td>
                        <td class="py-3">
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('admin.services.variants.edit', [$service, $variant]) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">{{ __('messages.edit') }}</a>
                                <form method="POST" action="{{ route('admin.services.variants.destroy', [$service, $variant]) }}" onsubmit="return confirm('{{ __('messages.confirm_delete_variant') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_variants_text') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
@endsection
