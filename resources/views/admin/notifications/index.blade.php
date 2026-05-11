@extends('layouts.app')

@section('title', 'سجل الإشعارات')

@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="سجل الإشعارات المرسلة" subtitle="كل الإشعارات التي أرسلها الأدمن للمستخدمين.">
            <x-slot:actions>
                <a href="{{ route('admin.notifications.create') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">إرسال إشعار</a>
            </x-slot:actions>
        </x-page-header>

        <div class="mt-6 space-y-4">
            @forelse ($sentNotifications as $notification)
                <div class="rounded-2xl border border-slate-50 p-4 dark:border-slate-700">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-slate-800 dark:text-white">{{ $notification->title_ar }}</h2>
                            <p class="mt-1 text-sm text-slate-900 dark:text-slate-50">{{ $notification->content_ar }}</p>
                            <p class="mt-2 text-xs text-slate-700 dark:text-slate-50">{{ $notification->scope }} - {{ $notification->recipient_count }} مستلم - بواسطة {{ $notification->admin?->name ?? '-' }}</p>
                        </div>
                        <span class="text-xs text-slate-100">{{ $notification->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if ($notification->image_path)
                        <img src="{{ asset('storage/'.$notification->image_path) }}" alt="notification" class="mt-3 h-32 w-auto rounded-lg border border-slate-50 object-contain">
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-900">لا توجد إشعارات مرسلة.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $sentNotifications->links() }}</div>
    </x-card>
@endsection
