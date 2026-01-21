<!doctype html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'كوين كارد')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50">
        @php
            $containerWidth = request()->routeIs('admin.*') ? 'max-w-7xl' : 'max-w-6xl';
            $mainWidth = trim($__env->yieldContent('mainWidth', $containerWidth));
        @endphp
        <div class="min-h-screen flex flex-col">
            <nav class="border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="mx-auto flex {{ $containerWidth }} flex-row-reverse items-center justify-between px-4 py-4">
                    <a href="{{ route('home') }}" class="text-lg font-bold text-emerald-700">
                        كوين كارد
                    </a>
                    <div class="hidden items-center gap-4 text-sm text-slate-700 md:flex">
                        <a href="{{ route('home') }}" class="cc-nav-link">الرئيسية</a>
                        @auth
                            <a href="{{ route('account') }}" class="cc-nav-link">حسابي</a>
                            @role('admin')
                                <a href="{{ route('admin.index') }}" class="cc-nav-link cc-nav-link-pill">لوحة الأدمن</a>
                            @endrole
                        @endauth
                         @auth
                            <div class="relative">
                                <x-button type="button" variant="secondary" data-dropdown-trigger="notifications-panel" class="cc-nav-link">
                                    الإشعارات
                                    @if (! empty($navUnreadCount ?? 0))
                                        <span class="absolute -left-1 -top-1 rounded-full bg-rose-500 px-2 text-xs text-white">{{ $navUnreadCount }}</span>
                                    @endif
                                </x-button>
                                <div id="notifications-panel" class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-80">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">آخر الإشعارات</p>
                                            <a href="{{ route('account.notifications') }}" class="text-xs text-emerald-700 hover:text-emerald-900">عرض الكل</a>
                                        </div>
                                        <div class="mt-3 space-y-3 text-sm min-w-0">
                                            @forelse ($navNotifications ?? [] as $notification)
                                                <a href="{{ $notification->data['url'] ?? route('account.notifications') }}" class="block rounded-xl border border-slate-200 p-3 transition hover:border-emerald-200">
                                                    <div class="flex items-start justify-between gap-2 min-w-0">
                                                        <p class="font-semibold text-slate-700 break-words">{{ $notification->data['title'] ?? 'إشعار جديد' }}</p>
                                                        @if ($notification->read_at === null)
                                                            <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-2 text-xs text-slate-500 break-words">{{ $notification->data['description'] ?? '' }}</p>
                                                    <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                                                </a>
                                            @empty
                                                <p class="text-xs text-slate-500">لا توجد إشعارات حتى الآن.</p>
                                            @endforelse
                                        </div>
                                        <form method="POST" action="{{ route('account.notifications.mark-all-read') }}" class="mt-4">
                                            @csrf
                                            <button type="submit" class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                                تعليم الكل كمقروء
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <x-button type="button" variant="ghost" data-dropdown-trigger="user-panel" class="cc-nav-link">
                                    {{ auth()->user()->name }}
                                </x-button>
                                <div id="user-panel" class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-48">
                                    <div class="p-2">
                                        <x-button type="button" variant="ghost" data-logout-button class="w-full justify-start">
                                            تسجيل الخروج
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl border border-emerald-200 px-4 py-2 text-emerald-700 transition hover:bg-emerald-50">تسجيل الدخول</a>
                            <a href="{{ route('register') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-white transition hover:brightness-105">إنشاء حساب</a>
                        @endauth
                         <a href="{{ route('privacy-policy') }}" class="cc-nav-link">سياسة الخصوصية</a>
                        <a href="{{ route('about') }}" class="cc-nav-link">من نحن</a>
                    </div>
                    <div class="hidden items-center gap-3 text-sm md:flex">
                        <!-- @auth
                            <div class="relative">
                                <x-button type="button" variant="secondary" data-dropdown-trigger="notifications-panel" class="rounded-full px-3 py-1">
                                    الإشعارات
                                    @if (! empty($navUnreadCount ?? 0))
                                        <span class="absolute -left-1 -top-1 rounded-full bg-rose-500 px-2 text-xs text-white">{{ $navUnreadCount }}</span>
                                    @endif
                                </x-button>
                                <div id="notifications-panel" class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-80">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">آخر الإشعارات</p>
                                            <a href="{{ route('account.notifications') }}" class="text-xs text-emerald-700 hover:text-emerald-900">عرض الكل</a>
                                        </div>
                                        <div class="mt-3 space-y-3 text-sm min-w-0">
                                            @forelse ($navNotifications ?? [] as $notification)
                                                <a href="{{ $notification->data['url'] ?? route('account.notifications') }}" class="block rounded-xl border border-slate-200 p-3 transition hover:border-emerald-200">
                                                    <div class="flex items-start justify-between gap-2 min-w-0">
                                                        <p class="font-semibold text-slate-700 break-words">{{ $notification->data['title'] ?? 'إشعار جديد' }}</p>
                                                        @if ($notification->read_at === null)
                                                            <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-2 text-xs text-slate-500 break-words">{{ $notification->data['description'] ?? '' }}</p>
                                                    <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                                                </a>
                                            @empty
                                                <p class="text-xs text-slate-500">لا توجد إشعارات حتى الآن.</p>
                                            @endforelse
                                        </div>
                                        <form method="POST" action="{{ route('account.notifications.mark-all-read') }}" class="mt-4">
                                            @csrf
                                            <button type="submit" class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                                تعليم الكل كمقروء
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <x-button type="button" variant="ghost" data-dropdown-trigger="user-panel" class="rounded-full px-3 py-1 text-emerald-700">
                                    {{ auth()->user()->name }}
                                </x-button>
                                <div id="user-panel" class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-48">
                                    <div class="p-2">
                                        <x-button type="button" variant="ghost" data-logout-button class="w-full justify-start text-slate-700">
                                            تسجيل الخروج
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl border border-emerald-200 px-4 py-2 text-emerald-700 transition hover:bg-emerald-50">تسجيل الدخول</a>
                            <a href="{{ route('register') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-white transition hover:brightness-105">إنشاء حساب</a>
                        @endauth -->
                    </div>
                    <div class="md:hidden">
                        <button type="button" data-dropdown-trigger="mobile-menu-panel" class="cursor-pointer cc-nav-link">القائمة</button>
                        <div id="mobile-menu-panel" class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-64">
                            <div class="p-2">
                                <a href="{{ route('home') }}" class="block cc-nav-link">الرئيسية</a>
                                @auth
                                    <a href="{{ route('account') }}" class="block cc-nav-link">حسابي</a>
                                    @role('admin')
                                        <a href="{{ route('admin.index') }}" class="block cc-nav-link cc-nav-link-pill">لوحة الأدمن</a>
                                    @endrole
                                    <button type="button" data-logout-button class="mt-2 w-full text-right cc-nav-link">
                                        تسجيل الخروج
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="block cc-nav-link">تسجيل الدخول</a>
                                    <a href="{{ route('register') }}" class="block cc-nav-link">إنشاء حساب</a>
                                @endauth
                                <a href="{{ route('privacy-policy') }}" class="block cc-nav-link">سياسة الخصوصية</a>
                                <a href="{{ route('about') }}" class="block cc-nav-link">من نحن</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="mx-auto {{ $mainWidth }} flex-1 px-4 py-10">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white py-6">
                <div class="mx-auto flex {{ $containerWidth }} flex-wrap items-center justify-between gap-3 px-4 text-sm text-slate-600">
                    <p>جميع الحقوق محفوظة.</p>
                </div>
            </footer>
        </div>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>

        <a href="https://wa.me/963991195136" target="_blank" rel="noopener noreferrer" class="fixed bottom-4 left-4 z-50 inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition duration-200 hover:brightness-105 motion-reduce:transition-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-5 w-5 fill-current" aria-hidden="true">
                <path d="M16 3.2a12.8 12.8 0 0 0-11.1 19.2L3.2 28.8l6.6-1.7A12.8 12.8 0 1 0 16 3.2Zm7.5 17.7c-.3.8-1.5 1.5-2.3 1.6-.6.1-1.4.2-4.7-1-4.1-1.6-6.7-5.8-6.9-6.1-.2-.3-1.7-2.2-1.7-4.2s1-3 1.3-3.4c.3-.3.6-.4.9-.4h.7c.2 0 .5 0 .8.6.3.6 1 2.4 1.1 2.6.1.2.1.5 0 .7-.1.2-.2.4-.4.6-.2.2-.4.4-.6.6-.2.2-.4.4-.2.7.2.3.9 1.5 2 2.4 1.4 1.2 2.5 1.6 2.9 1.8.4.2.6.2.8 0 .2-.2 1-1.1 1.2-1.5.2-.4.5-.3.8-.2.3.1 2.1 1 2.4 1.2.3.2.5.3.6.5.1.2.1.9-.2 1.7Z"/>
            </svg>
            تواصل واتساب
        </a>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const overlay = document.getElementById('dropdown-overlay');
                const triggers = document.querySelectorAll('[data-dropdown-trigger]');
                const panels = new Map();

                triggers.forEach((trigger) => {
                    const panelId = trigger.getAttribute('data-dropdown-trigger');
                    const panel = document.getElementById(panelId);
                    if (panel) {
                        panels.set(trigger, panel);
                    }
                });

                const closeAll = () => {
                    panels.forEach((panel, trigger) => {
                        panel.classList.add('hidden');
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                    overlay?.classList.add('hidden');
                };

                triggers.forEach((trigger) => {
                    const panel = panels.get(trigger);
                    if (!panel) {
                        return;
                    }

                    trigger.addEventListener('click', (event) => {
                        event.stopPropagation();
                        const isOpen = !panel.classList.contains('hidden');
                        closeAll();
                        if (!isOpen) {
                            panel.classList.remove('hidden');
                            trigger.setAttribute('aria-expanded', 'true');
                            overlay?.classList.remove('hidden');
                        }
                    });

                    panel.addEventListener('click', (event) => {
                        event.stopPropagation();
                    });
                });

                overlay?.addEventListener('click', closeAll);
                document.querySelectorAll('[data-logout-button]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const form = document.getElementById('logout-form');
                        if (form) {
                            form.submit();
                        }
                    });
                });
                document.addEventListener('click', (event) => {
                    const target = event.target;
                    const clickedInside = Array.from(panels.values()).some((panel) => panel.contains(target)) ||
                        Array.from(triggers).some((trigger) => trigger.contains(target));
                    if (!clickedInside) {
                        closeAll();
                    }
                });
            });
        </script>
    </body>
</html>
