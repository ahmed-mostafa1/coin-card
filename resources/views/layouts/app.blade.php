<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Arab 8BP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="min-h-screen bg-slate-50 pb-20 md:pb-24">
    @php
        $containerWidth = request()->routeIs('admin.*') ? 'max-w-7xl' : 'max-w-6xl';
        $mainWidth = trim($__env->yieldContent('mainWidth', $containerWidth));
    @endphp
    <div class="min-h-screen flex flex-col">
        <nav class="border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex {{ $containerWidth }} items-center justify-between px-4 py-4">
                <a href="{{ route('home') }}" class="text-lg font-bold text-emerald-700">
                    Arab 8BP
                </a>
                <div class="hidden items-center gap-4 text-sm text-slate-700 md:flex">
                    <a href="{{ route('home') }}" class="cc-nav-link">{{ __('messages.home') }}</a>
                    @auth
                        <a href="{{ route('account') }}" class="cc-nav-link">{{ __('messages.my_account') }}</a>
                        @role('admin')
                        <a href="{{ route('admin.index') }}"
                            class="cc-nav-link cc-nav-link-pill">{{ __('messages.admin_panel') }}</a>
                        @endrole
                    @endauth
                    @auth
                        <div class="relative">
                            <x-button type="button" variant="secondary" data-dropdown-trigger="notifications-panel"
                                class="cc-nav-link">
                                {{ __('messages.notifications') }}
                                @if (!empty($navUnreadCount ?? 0))
                                    <span
                                        class="absolute -left-1 -top-1 rounded-full bg-rose-500 px-2 text-xs text-white">{{ $navUnreadCount }}</span>
                                @endif
                            </x-button>
                            <div id="notifications-panel"
                                class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-80">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ __('messages.latest_notifications') }}</p>
                                        <a href="{{ route('account.notifications') }}"
                                            class="text-xs text-emerald-700 hover:text-emerald-900">{{ __('messages.view_all') }}</a>
                                    </div>
                                    <div class="mt-3 space-y-3 text-sm min-w-0">
                                        @forelse ($navNotifications ?? [] as $notification)
                                            <a href="{{ $notification->data['url'] ?? route('account.notifications') }}"
                                                class="block rounded-xl border border-slate-200 p-3 transition hover:border-emerald-200">
                                                <div class="flex items-start justify-between gap-2 min-w-0">
                                                    <p class="font-semibold text-slate-700 break-words">
                                                        {{ trans($notification->data['title'] ?? 'messages.new_notification', $notification->data['title_params'] ?? []) }}
                                                    </p>
                                                    @if ($notification->read_at === null)
                                                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                                                    @endif
                                                </div>
                                                <p class="mt-2 text-xs text-slate-500 break-words">
                                                    {{ trans($notification->data['description'] ?? '', $notification->data['description_params'] ?? []) }}
                                                    @if(isset($notification->data['admin_note']) && !empty($notification->data['admin_note']))
                                                        {{ trans('messages.notifications_custom.deposit_rejected_reason', ['reason' => $notification->data['admin_note']]) }}
                                                    @endif
                                                </p>
                                                <p class="mt-2 text-xs text-slate-400">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </a>
                                        @empty
                                            <p class="text-xs text-slate-500">{{ __('messages.no_notifications') }}</p>
                                        @endforelse
                                    </div>
                                    <form method="POST" action="{{ route('account.notifications.mark-all-read') }}"
                                        class="mt-4">
                                        @csrf
                                        <button type="submit"
                                            class="w-full rounded-xl border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                            {{ __('messages.mark_all_read') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <x-button type="button" variant="ghost" data-dropdown-trigger="user-panel" class="cc-nav-link">
                                {{ auth()->user()->name }}
                            </x-button>
                            <div id="user-panel"
                                class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-48">
                                <div class="p-2">
                                    <x-button type="button" variant="ghost" data-logout-button class="w-full justify-start">
                                        {{ __('messages.logout') }}
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="rounded-xl border border-emerald-200 px-4 py-2 text-emerald-700 transition hover:bg-emerald-50">{{ __('messages.login') }}</a>
                        <a href="{{ route('register') }}"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-white transition hover:brightness-105">{{ __('messages.register') }}</a>
                    @endauth
                    <a href="{{ route('privacy-policy') }}" class="cc-nav-link">{{ __('messages.privacy_policy') }}</a>
                    <a href="{{ route('about') }}" class="cc-nav-link">{{ __('messages.about_us') }}</a>

                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="cc-nav-link text-emerald-600 font-bold">English</a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}"
                            class="cc-nav-link text-emerald-600 font-bold">العربية</a>
                    @endif
                </div>
                <!-- Desktop Hidden Menu items (removed duplicate section that was hidden md:flex) -->

                <div class="md:hidden">
                    <button type="button" data-dropdown-trigger="mobile-menu-panel"
                        class="cursor-pointer cc-nav-link">{{ __('messages.menu') }}</button>
                    <div id="mobile-menu-panel"
                        class="hidden fixed inset-x-3 top-16 z-50 w-auto max-w-[calc(100vw-1.5rem)] rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden sm:absolute sm:inset-x-auto sm:top-full sm:mt-2 sm:right-0 sm:w-64">
                        <div class="p-2">
                            <a href="{{ route('home') }}" class="block cc-nav-link">{{ __('messages.home') }}</a>
                            @auth
                                <a href="{{ route('account') }}"
                                    class="block cc-nav-link">{{ __('messages.my_account') }}</a>
                                @role('admin')
                                <a href="{{ route('admin.index') }}"
                                    class="block cc-nav-link cc-nav-link-pill">{{ __('messages.admin_panel') }}</a>
                                @endrole
                                <button type="button" data-logout-button
                                    class="mt-2 w-full {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} cc-nav-link">
                                    {{ __('messages.logout') }}
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="block cc-nav-link">{{ __('messages.login') }}</a>
                                <a href="{{ route('register') }}"
                                    class="block cc-nav-link">{{ __('messages.register') }}</a>
                            @endauth
                            <a href="{{ route('privacy-policy') }}"
                                class="block cc-nav-link">{{ __('messages.privacy_policy') }}</a>
                            <a href="{{ route('about') }}" class="block cc-nav-link">{{ __('messages.about_us') }}</a>
                            <div class="border-t border-slate-100 my-2 pt-2">
                                @if(app()->getLocale() == 'ar')
                                    <a href="{{ route('lang.switch', 'en') }}" class="block cc-nav-link">English</a>
                                @else
                                    <a href="{{ route('lang.switch', 'ar') }}" class="block cc-nav-link">العربية</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="mx-auto {{ $mainWidth }} flex-1 px-4 py-10">
            @yield('content')
        </main>

        <!-- New Sticky Footer -->
        <footer class="fixed bottom-0 left-0 right-0 z-50 border-t border-slate-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
            
            <!-- Desktop Layout -->
            <div class="hidden md:flex mx-auto {{ $containerWidth }} h-20 items-center px-4">
                <div class="flex items-center justify-between w-full gap-6 text-slate-500">
                    <!-- Orders/Cart -->
                    <a href="{{ route('account.orders') }}" 
                       class="{{ request()->routeIs('account.orders*') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white scale-110' : 'bg-slate-100 hover:bg-slate-200 hover:text-slate-800' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-basket-shopping text-xl"></i>
                    </a>

                    <!-- Notifications -->
                    <a href="{{ route('account.notifications') }}" 
                       class="{{ request()->routeIs('account.notifications*') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white scale-110' : 'bg-slate-100 hover:bg-slate-200 hover:text-slate-800' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-bell text-xl"></i>
                        @if (!empty($navUnreadCount ?? 0))
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] text-white">{{ $navUnreadCount }}</span>
                        @endif
                    </a>

                    <!-- Home (Center) -->
                    <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-1 group">
                         <div class="{{ request()->routeIs('home') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white scale-110' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800' }} flex h-14 w-14 items-center justify-center rounded-full transition group-hover:scale-110">
                            <i class="fa-solid fa-home text-2xl"></i>
                         </div>
                         <span class="{{ request()->routeIs('home') ? 'text-orange-500' : 'text-slate-500' }} text-xs font-bold">{{ __('messages.home') }}</span>
                    </a>

                    <!-- Balance -->
                    <a href="{{ route('account.wallet') }}" 
                        class="{{ request()->routeIs('account.wallet*') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white scale-110' : 'bg-slate-100 hover:bg-slate-200 hover:text-slate-800' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </a>

                    <!-- Account -->
                    <a href="{{ route('account') }}" 
                        class="{{ request()->routeIs('account') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white scale-110' : 'bg-slate-100 hover:bg-slate-200 hover:text-slate-800' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-user text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Mobile Layout -->
             <div class="flex md:hidden h-16 w-full items-center justify-between px-2">
                <div class="grid grid-cols-5 w-full items-center justify-items-center">
                    
                    <!-- Orders -->
                    <div class="relative w-full flex justify-center">
                        @if(request()->routeIs('account.orders*'))
                            <div class="relative -top-6">
                                <a href="{{ route('account.orders') }}" class="flex flex-col items-center justify-center gap-1">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-400 shadow-lg ring-4 ring-white transition active:scale-95">
                                        <i class="fa-solid fa-basket-shopping text-2xl text-white"></i>
                                    </div>
                                    <span class="text-[10px] font-bold text-orange-400">Orders</span>
                                </a>
                            </div>
                        @else
                            <a href="{{ route('account.orders') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition active:scale-95">
                                <i class="fa-solid fa-basket-shopping text-lg"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Notifications -->
                    <div class="relative w-full flex justify-center">
                        @if(request()->routeIs('account.notifications*'))
                             <div class="relative -top-6">
                                <a href="{{ route('account.notifications') }}" class="flex flex-col items-center justify-center gap-1">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-400 shadow-lg ring-4 ring-white transition active:scale-95">
                                        <i class="fa-solid fa-bell text-2xl text-white"></i>
                                    </div>
                                    <span class="text-[10px] font-bold text-orange-400">Alerts</span>
                                </a>
                             </div>
                        @else
                             <a href="{{ route('account.notifications') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition active:scale-95">
                                <i class="fa-solid fa-bell text-lg"></i>
                                 @if (!empty($navUnreadCount ?? 0))
                                    <span class="absolute top-0 right-0 flex h-3 w-3 items-center justify-center rounded-full bg-rose-500 text-[8px] text-white">{{ $navUnreadCount }}</span>
                                @endif
                            </a>
                        @endif
                    </div>

                    <!-- Home -->
                    <div class="relative w-full flex justify-center">
                        @if(request()->routeIs('home'))
                             <div class="relative -top-6">
                                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-1">
                                     <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-400 shadow-lg ring-4 ring-white transition active:scale-95">
                                         <i class="fa-solid fa-home text-2xl text-white"></i>
                                     </div>
                                     <span class="text-[10px] font-bold text-orange-400">{{ __('messages.home') }}</span>
                                </a>
                             </div>
                        @else
                            <a href="{{ route('home') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition active:scale-95">
                                <i class="fa-solid fa-home text-lg"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Balance -->
                    <div class="relative w-full flex justify-center">
                         @if(request()->routeIs('account.wallet*'))
                             <div class="relative -top-6">
                                <a href="{{ route('account.wallet') }}" class="flex flex-col items-center justify-center gap-1">
                                     <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-400 shadow-lg ring-4 ring-white transition active:scale-95">
                                         <i class="fa-solid fa-wallet text-2xl text-white"></i>
                                     </div>
                                     <span class="text-[10px] font-bold text-orange-400">Balance</span>
                                </a>
                             </div>
                        @else
                            <a href="{{ route('account.wallet') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition active:scale-95">
                                <i class="fa-solid fa-wallet text-lg"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Account -->
                    <div class="relative w-full flex justify-center">
                         @if(request()->routeIs('account') && !request()->routeIs('account.orders*') && !request()->routeIs('account.notifications*') && !request()->routeIs('account.wallet*'))
                             <div class="relative -top-6">
                                <a href="{{ route('account') }}" class="flex flex-col items-center justify-center gap-1">
                                     <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-400 shadow-lg ring-4 ring-white transition active:scale-95">
                                         <i class="fa-solid fa-user text-2xl text-white"></i>
                                     </div>
                                     <span class="text-[10px] font-bold text-orange-400">Account</span>
                                </a>
                             </div>
                        @else
                            <a href="{{ route('account') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition active:scale-95">
                                <i class="fa-solid fa-user text-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    <a href="https://wa.me/963991195136" target="_blank" rel="noopener noreferrer"
        class="fixed bottom-24 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} z-50 inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition duration-200 hover:brightness-105 motion-reduce:transition-none">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-5 w-5 fill-current" aria-hidden="true">
            <path
                d="M16 3.2a12.8 12.8 0 0 0-11.1 19.2L3.2 28.8l6.6-1.7A12.8 12.8 0 1 0 16 3.2Zm7.5 17.7c-.3.8-1.5 1.5-2.3 1.6-.6.1-1.4.2-4.7-1-4.1-1.6-6.7-5.8-6.9-6.1-.2-.3-1.7-2.2-1.7-4.2s1-3 1.3-3.4c.3-.3.6-.4.9-.4h.7c.2 0 .5 0 .8.6.3.6 1 2.4 1.1 2.6.1.2.1.5 0 .7-.1.2-.2.4-.4.6-.2.2-.4.4-.6.6-.2.2-.4.4-.2.7.2.3.9 1.5 2 2.4 1.4 1.2 2.5 1.6 2.9 1.8.4.2.6.2.8 0 .2-.2 1-1.1 1.2-1.5.2-.4.5-.3.8-.2.3.1 2.1 1 2.4 1.2.3.2.5.3.6.5.1.2.1.9-.2 1.7Z" />
        </svg>
        {{ __('messages.contact_whatsapp') }}
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                    }
                });

                panel.addEventListener('click', (event) => {
                    event.stopPropagation();
                });
            });


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

            closeAll();
        });
    </script>
    @stack('scripts')
</body>

</html>
