@extends('layouts.app')

@section('title', 'المستخدمون')


@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.users_management') }}</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('messages.users_management_desc') }}</p>
                </div>
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <x-text-input name="q" type="text" placeholder="{{ __('messages.search_name_email') }}" :value="$search" />
                    <x-primary-button>{{ __('messages.search_button') }}</x-primary-button>
                </form>
            </div>
        </div>

        <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-6 shadow-sm">
            <x-table>
                <thead class="border-b border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="py-2">{{ __('messages.user_label') }}</th>
                        <th class="py-2">{{ __('messages.status') }}</th>
                        <th class="py-2">{{ __('messages.creation_date') }}</th>
                        <th class="py-2">{{ __('messages.view_link') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($users as $user)
                        <tr>
                            <td class="py-3 text-slate-700 dark:text-white">
                                {{ $user->name }}
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                            </td>
                            <td class="py-3">
                                @if ($user->is_banned)
                                    <span class="rounded-full bg-rose-100 dark:bg-rose-900/50 px-3 py-1 text-xs text-rose-700 dark:text-rose-400">{{ __('messages.status_banned') }}</span>
                                @endif
                                @if ($user->is_frozen)
                                    <span class="rounded-full bg-amber-100 dark:bg-amber-900/50 px-3 py-1 text-xs text-amber-700 dark:text-amber-400">{{ __('messages.status_frozen') }}</span>
                                @endif
                                @if (! $user->is_banned && ! $user->is_frozen)
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1 text-xs text-emerald-700 dark:text-emerald-400">{{ __('messages.active_user') }}</span>
                                @endif
                            </td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">{{ __('messages.view_link') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-500 dark:text-slate-400">{{ __('messages.no_users_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-6">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
