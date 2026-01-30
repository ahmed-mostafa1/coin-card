@extends('layouts.app')

@section('title', 'الإشعارات')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">الإشعارات</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">متابعة تنبيهات الشحن والطلبات.</p>
            </div>
            <form method="POST" action="{{ route('account.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="rounded-full border border-emerald-200 dark:border-emerald-700 px-4 py-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400 transition hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                    تعليم الكل كمقروء
                </button>
            </form>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 flex items-center gap-3 text-sm">
            <a href="{{ route('account.notifications', ['filter' => 'all']) }}"
                class="rounded-full border px-4 py-2 transition {{ $filter === 'all' ? 'border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">
                الكل
            </a>
            <a href="{{ route('account.notifications', ['filter' => 'unread']) }}"
                class="rounded-full border px-4 py-2 transition {{ $filter === 'unread' ? 'border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-emerald-200 dark:hover:border-emerald-700' }}">
                غير مقروء
            </a>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? route('account.notifications') }}" class="block rounded-2xl border border-slate-200 dark:border-slate-700 p-4 transition hover:border-emerald-200 dark:hover:border-emerald-700">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-700 dark:text-white">{{ $notification->data['title'] ?? 'إشعار جديد' }}</p>
                        @if ($notification->read_at === null)
                            <span class="mt-1 inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/50 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">جديد</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $notification->data['description'] ?? '' }}</p>
                    <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد إشعارات حتى الآن.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
