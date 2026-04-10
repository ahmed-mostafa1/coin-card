<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.seo-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>[x-cloak] { display: none !important; }</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        const isDark = localStorage.theme === 'dark' || (!('theme' in localStorage) ? window.matchMedia('(prefers-color-scheme: dark)').matches : false);
        if (isDark) document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
    </script>
</head>

<body class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-200"
      x-data="{
          darkMode: localStorage.theme === 'dark' || (localStorage.theme ? false : window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="$watch('darkMode', val => {
          localStorage.theme = val ? 'dark' : 'light';
          if (val) document.documentElement.classList.add('dark');
          else document.documentElement.classList.remove('dark');
      })">

    @php
        $containerWidth = 'w-[85%] mx-auto';
        $mainWidth = trim($__env->yieldContent('mainWidth', $containerWidth));
        $isAr = app()->getLocale() == 'ar';
        $currentUser = auth()->user();
        $isAdmin = $currentUser?->hasRole('admin') ?? false;
        $logoTextValue = filled(trim($sharedLogoText ?? '')) ? trim($sharedLogoText) : config('app.name', 'Coin Card');
        $logoImageUrl = $sharedLogoType === 'image' && filled($sharedLogoImage ?? null)
            ? asset('storage/' . $sharedLogoImage)
            : null;
        $languageToggleLocale = $isAr ? 'en' : 'ar';
        $languageToggleLabel = $isAr ? 'EN' : 'AR';
        $languageToggleTitle = $isAr ? 'Switch to English' : 'التبديل إلى العربية';
        $legalLinks = [
            [
                'route' => route('about'),
                'label' => __('messages.about_us'),
                'active' => request()->routeIs('about'),
            ],
            [
                'route' => route('contact-us.show'),
                'label' => __('messages.contact_page'),
                'active' => request()->routeIs('contact-us.*'),
            ],
            [
                'route' => route('privacy-policy'),
                'label' => __('messages.privacy_policy'),
                'active' => request()->routeIs('privacy-policy'),
            ],
            [
                'route' => route('terms-of-use'),
                'label' => app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions',
                'active' => request()->routeIs('terms-of-use') || request()->routeIs('terms-and-conditions'),
            ],
        ];
    @endphp

    @auth
        @inject('vipService', 'App\Services\VipService')
        @php
            $menuVip = $vipService->getVipSummary($currentUser);
            $menuTier = $menuVip['current_tier'];
            $menuNextTier = $menuVip['next_tier'];
            $menuProgress = $menuVip['progress_percent'];
            $menuTierLabel = $menuTier
                ? ($isAr ? $menuTier->title_ar : ($menuTier->title_en ?: $menuTier->title_ar))
                : ($isAr ? 'بدون مستوى' : 'No Rank');
            $profileAvatar = filled($currentUser->avatar ?? null) ? $currentUser->avatar : null;
            $profileInitial = mb_substr(trim($currentUser->name ?? 'U'), 0, 1);
            $navUnreadCountValue = min((int) ($navUnreadCount ?? 0), 99);
            $storeActive = request()->routeIs('home')
                || request()->routeIs('categories.show')
                || request()->routeIs('services.show')
                || request()->routeIs('services.purchase');
            $profileChipActive = request()->routeIs('account*');

            $adminOpsActive = request()->routeIs('admin.ops.*');
            $adminOrdersActive = request()->routeIs('admin.orders*');
            $adminDepositsActive = request()->routeIs('admin.deposits*');
            $adminUsersActive = request()->routeIs('admin.users*');
            $adminReportsActive = request()->routeIs('admin.reports*');
            $adminDashboardActive = request()->routeIs('admin.*')
                && ! $adminOpsActive
                && ! $adminOrdersActive
                && ! $adminDepositsActive
                && ! $adminUsersActive
                && ! $adminReportsActive;

            $topNavLinks = $isAdmin
                ? [
                    [
                        'route' => route('admin.index'),
                        'label' => __('messages.admin_dashboard'),
                        'icon' => 'fa-solid fa-gauge-high',
                        'active' => $adminDashboardActive,
                    ],
                    [
                        'route' => route('admin.ops.index'),
                        'label' => $isAr ? 'العمليات' : 'Operations',
                        'icon' => 'fa-solid fa-bolt',
                        'active' => $adminOpsActive,
                    ],
                    [
                        'route' => route('admin.orders.index'),
                        'label' => $isAr ? 'الطلبات' : 'Orders',
                        'icon' => 'fa-solid fa-basket-shopping',
                        'active' => $adminOrdersActive,
                    ],
                    [
                        'route' => route('admin.deposits.index'),
                        'label' => $isAr ? 'الشحن' : 'Deposits',
                        'icon' => 'fa-solid fa-wallet',
                        'active' => $adminDepositsActive,
                    ],
                ]
                : [
                    [
                        'route' => route('home'),
                        'label' => __('messages.home'),
                        'icon' => 'fa-solid fa-house',
                        'active' => $storeActive,
                    ],
                    [
                        'route' => route('account.orders'),
                        'label' => __('messages.my_orders'),
                        'icon' => 'fa-solid fa-basket-shopping',
                        'active' => request()->routeIs('account.orders*'),
                    ],
                    [
                        'route' => route('account.wallet'),
                        'label' => __('messages.wallet'),
                        'icon' => 'fa-solid fa-wallet',
                        'active' => request()->routeIs('account.wallet*'),
                    ],
                    [
                        'route' => route('account.notifications'),
                        'label' => __('messages.notifications'),
                        'icon' => 'fa-solid fa-bell',
                        'active' => request()->routeIs('account.notifications*'),
                        'badge' => $navUnreadCountValue,
                    ],
                ];

            $menuLinks = [
                [
                    'route' => route('account'),
                    'label' => __('messages.my_account'),
                    'icon' => 'fa-solid fa-id-card',
                    'active' => $profileChipActive,
                ],
            ];

            if ($isAdmin) {
                $menuLinks = array_merge($menuLinks, [
                    [
                        'route' => route('account.notifications'),
                        'label' => __('messages.notifications'),
                        'icon' => 'fa-solid fa-bell',
                        'active' => request()->routeIs('account.notifications*'),
                        'badge' => $navUnreadCountValue,
                    ],
                    [
                        'route' => route('admin.reports.index'),
                        'label' => $isAr ? 'التقارير' : 'Reports',
                        'icon' => 'fa-solid fa-chart-line',
                        'active' => $adminReportsActive,
                    ],
                    [
                        'route' => route('admin.site-settings.edit'),
                        'label' => $isAr ? 'إدارة الموقع' : 'Site Settings',
                        'icon' => 'fa-solid fa-sliders',
                        'active' => request()->routeIs('admin.site-settings.*') || request()->routeIs('admin.appearance.*'),
                    ],
                    [
                        'route' => route('home'),
                        'label' => $isAr ? 'الواجهة العامة' : 'Storefront',
                        'icon' => 'fa-solid fa-store',
                        'active' => false,
                    ],
                ]);
            }
        @endphp
    @endauth

    <div class="min-h-screen flex flex-col">
        <nav class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-md transition-colors duration-200 dark:border-slate-700/80 dark:bg-slate-900/95">
            <div class="mx-auto w-[92%] py-3 sm:w-[85%] sm:px-4">
                <div class="rounded-[1.75rem] border border-white/70 bg-white/90 px-3 py-3 shadow-[0_18px_40px_rgba(15,23,42,0.06)] backdrop-blur dark:border-slate-800/80 dark:bg-slate-950/90 sm:px-4">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 sm:gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl">
                                @if($logoImageUrl)
                                    <img src="{{ $logoImageUrl }}"
                                         alt="{{ $logoTextValue }}"
                                         class="h-full w-full object-contain p-1.5">
                                @else
                                    <span class="text-sm font-black text-emerald-700 dark:text-emerald-300">
                                        {{ mb_substr($logoTextValue, 0, 1) }}
                                    </span>
                                @endif
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-black tracking-[0.01em] text-slate-950 dark:text-white sm:text-base">
                                    {{ $logoTextValue }}
                                </span>
                                <span class="hidden truncate text-[11px] font-medium text-slate-500 dark:text-slate-400 sm:block">
                                    {{ $isAr ? 'شحن ألعاب وتطبيقات ' : 'Games and Apps topup' }}
                                </span>
                            </span>
                        </a>

                        @auth
                            <div class="hidden min-w-0 flex-1 items-center justify-center lg:flex">
                                <div class="flex items-center gap-1.5 overflow-x-auto px-2 pb-1 scrollbar-none" style="-ms-overflow-style:none;scrollbar-width:none;">
                                    @foreach($topNavLinks as $link)
                                        <a href="{{ $link['route'] }}"
                                           class="cc-nav-tab {{ $link['active'] ? 'cc-nav-tab-active' : '' }}">
                                            <i class="{{ $link['icon'] }} text-sm"></i>
                                            <span>{{ $link['label'] }}</span>
                                            @if(!empty($link['badge']))
                                                <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">
                                                    {{ $link['badge'] }}
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endauth

                        <div class="flex shrink-0 items-center gap-2">
                            @auth
                            <button type="button"
                                    @click="darkMode = !darkMode"
                                    class="cc-nav-utility"
                                    aria-label="{{ $isAr ? 'تبديل السمة' : 'Toggle theme' }}">
                                <i class="fa-solid fa-sun text-sm" x-show="!darkMode" x-cloak></i>
                                <i class="fa-solid fa-moon text-sm" x-show="darkMode" x-cloak></i>
                            </button>
                            @endauth

                            @auth
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button"
                                            @click="open = !open"
                                            :aria-expanded="open.toString()"
                                            aria-label="{{ $isAr ? 'الملف الشخصي والقائمة' : 'Profile menu' }}"
                                            class="cc-profile-chip {{ $profileChipActive ? 'cc-profile-chip-active' : '' }}">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-emerald-600 text-sm font-black text-white">
                                            @if($profileAvatar)
                                                <img src="{{ $profileAvatar }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                                            @else
                                                {{ $profileInitial }}
                                            @endif
                                        </span>
                                        <span class="hidden min-w-0 md:block">
                                            <span class="block max-w-28 truncate text-sm font-bold text-slate-900 dark:text-white">
                                                {{ $currentUser->name }}
                                            </span>
                                            <span class="block truncate text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                                {{ $menuTierLabel }}
                                            </span>
                                        </span>
                                        @if($navUnreadCountValue > 0)
                                            <span class="absolute -end-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">
                                                {{ $navUnreadCountValue }}
                                            </span>
                                        @endif
                                    </button>

                                    <div x-show="open"
                                         x-cloak
                                         x-transition.origin.top.right
                                         @click.outside="open = false"
                                         @keydown.escape.window="open = false"
                                         class="absolute end-0 top-full z-50 mt-3 w-[min(22rem,calc(100vw-1rem))] overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 sm:w-[23rem]">
                                        <div class="flex items-start gap-3">
                                            <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-[1.15rem] bg-emerald-600 text-lg font-black text-white">
                                                @if($profileAvatar)
                                                    <img src="{{ $profileAvatar }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                                                @else
                                                    {{ $profileInitial }}
                                                @endif
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-base font-bold text-slate-950 dark:text-white">{{ $currentUser->name }}</p>
                                                <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $currentUser->email }}</p>
                                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                                        <i class="fa-solid fa-crown text-[11px] text-amber-400"></i>
                                                        {{ $menuTierLabel }}
                                                    </span>
                                                    @if($navUnreadCountValue > 0)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-950/40 dark:text-rose-300">
                                                            <i class="fa-solid fa-bell text-[11px]"></i>
                                                            {{ $navUnreadCountValue }} {{ $isAr ? 'غير مقروء' : 'Unread' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-2">
                                            <div class="rounded-[1.15rem] border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/80">
                                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ __('messages.available_balance') }}</span>
                                                <p class="mt-1 text-sm font-bold text-emerald-700 dark:text-emerald-300" dir="ltr">$ {{ number_format($currentUser->available_balance, 2) }}</p>
                                            </div>
                                            <div class="rounded-[1.15rem] border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/80">
                                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ __('messages.held_balance') }}</span>
                                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white" dir="ltr">$ {{ number_format($currentUser->wallet?->held_balance ?? 0, 2) }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-2">
                                            <a href="{{ route('lang.switch', $languageToggleLocale) }}"
                                               class="flex items-center gap-3 rounded-[1rem] border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300">
                                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-200">
                                                    <i class="fa-solid fa-language text-sm"></i>
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ $isAr ? 'اللغة' : 'Language' }}</span>
                                                    <span class="block truncate">{{ $languageToggleLabel }}</span>
                                                </span>
                                            </a>

                                            <button type="button"
                                                    @click="darkMode = !darkMode"
                                                    class="flex items-center gap-3 rounded-[1rem] border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300">
                                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-200">
                                                    <i class="fa-solid fa-sun text-sm" x-show="!darkMode" x-cloak></i>
                                                    <i class="fa-solid fa-moon text-sm" x-show="darkMode" x-cloak></i>
                                                </span>
                                                <span class="min-w-0 text-start">
                                                    <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ $isAr ? 'المظهر' : 'Theme' }}</span>
                                                    <span class="block truncate" x-text="darkMode ? '{{ $isAr ? 'داكن' : 'Dark' }}' : '{{ $isAr ? 'فاتح' : 'Light' }}'"></span>
                                                </span>
                                            </button>
                                        </div>

                                        @if(!empty($menuLinks))
                                            <div class="mt-4 space-y-1.5">
                                                @foreach($menuLinks as $link)
                                                    <a href="{{ $link['route'] }}"
                                                       class="flex items-center justify-between rounded-[1rem] px-3 py-3 text-sm font-semibold transition {{ $link['active'] ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                                                        <span class="flex min-w-0 items-center gap-3">
                                                            <i class="{{ $link['icon'] }} text-sm shrink-0"></i>
                                                            <span class="truncate">{{ $link['label'] }}</span>
                                                        </span>
                                                        @if(!empty($link['badge']))
                                                            <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">
                                                                {{ $link['badge'] }}
                                                            </span>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                                            @csrf
                                            <button type="submit"
                                                    class="flex w-full items-center justify-center gap-2 rounded-[1rem] bg-slate-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-slate-600 dark:text-emerald-900 dark:hover:bg-slate-100">
                                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                                                <span>{{ __('messages.logout') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                {{-- Language icon-only: hidden on mobile, shown on sm+ --}}
                                <a href="{{ route('lang.switch', $languageToggleLocale) }}"
                                   class="cc-nav-utility hidden sm:inline-flex"
                                   aria-label="{{ $languageToggleTitle }}"
                                   title="{{ $languageToggleTitle }}">
                                    <i class="fa-solid fa-globe text-sm"></i>
                                </a>

                                {{-- Auth dropdown --}}
                                <div class="relative" x-data="{ guestOpen: false }">
                                    <button type="button"
                                            @click="guestOpen = !guestOpen"
                                            class="cc-nav-cta cc-nav-cta-primary flex items-center gap-1.5">
                                        <i class="fa-solid fa-right-to-bracket text-sm"></i>
                                        <span>{{ __('messages.login') }}</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="guestOpen ? 'rotate-180' : ''"></i>
                                    </button>

                                    <div x-show="guestOpen"
                                         x-cloak
                                         x-transition.origin.top.end
                                         @click.outside="guestOpen = false"
                                         @keydown.escape.window="guestOpen = false"
                                         class="absolute end-0 top-full z-50 mt-2 w-52 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">

                                        <a href="{{ route('login') }}"
                                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-100 dark:hover:bg-emerald-950/30 dark:hover:text-emerald-300">
                                            <i class="fa-solid fa-right-to-bracket w-4 text-center text-slate-400"></i>
                                            <span>{{ __('messages.login') }}</span>
                                        </a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}"
                                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 hover:text-slate-950 dark:text-slate-100 dark:hover:bg-slate-800 dark:hover:text-white">
                                                <i class="fa-solid fa-user-plus w-4 text-center text-slate-400"></i>
                                                <span>{{ __('messages.register') }}</span>
                                            </a>
                                        @endif

                                        <div class="mt-1 border-t border-slate-100 pt-1 dark:border-slate-800">
                                            {{-- Language switch: only in dropdown on mobile --}}
                                            <a href="{{ route('lang.switch', $languageToggleLocale) }}"
                                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 hover:text-slate-950 dark:text-slate-100 dark:hover:bg-slate-800 sm:hidden">
                                                <i class="fa-solid fa-globe w-4 text-center text-slate-400"></i>
                                                <span>{{ $languageToggleLabel }}</span>
                                            </a>

                                            <button type="button"
                                                    @click="darkMode = !darkMode; guestOpen = false"
                                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800">
                                                <i class="fa-solid fa-sun w-4 text-center text-slate-400" x-show="!darkMode" x-cloak></i>
                                                <i class="fa-solid fa-moon w-4 text-center text-slate-400" x-show="darkMode" x-cloak></i>
                                                <span x-text="darkMode ? '{{ $isAr ? 'داكن' : 'Dark' }}' : '{{ $isAr ? 'فاتح' : 'Light' }}'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>

                </div>
            </div>
        </nav>

        <main class="flex-1 pb-24 pt-4 sm:pt-6 sm:pb-24 lg:py-6 {{ $mainWidth }}">
            @yield('content')
        </main>

        <footer class="mb-24 border-t border-slate-200 bg-white/90 px-4 py-5 backdrop-blur-sm dark:border-slate-800 dark:bg-slate-900/90 lg:mb-0">
            <div class="mx-auto flex w-[85%] max-w-none flex-col items-center justify-between gap-3 text-center sm:flex-row sm:text-start">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                 {{ __('messages.all_rights_reserved') ?? 'All rights reserved.' }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end">
                    @foreach($legalLinks as $link)
                        <a href="{{ $link['route'] }}"
                           class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-emerald-700 dark:hover:text-emerald-300">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </footer>

        @auth
            <div class="fixed inset-x-0 bottom-0 z-40 px-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] pt-2 lg:hidden">
                <div class="rounded-[1.75rem] border border-white/70 bg-white/95 p-2 shadow-[0_-12px_30px_rgba(15,23,42,0.12)] backdrop-blur dark:border-slate-800/80 dark:bg-slate-950/95">
                    <div class="grid grid-cols-4 gap-1">
                        @foreach($topNavLinks as $link)
                            <a href="{{ $link['route'] }}"
                               class="relative flex min-w-0 flex-col items-center justify-center gap-1 rounded-2xl px-1 py-2 text-[11px] font-semibold transition {{ $link['active'] ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                                <span class="relative inline-flex h-5 items-center justify-center">
                                    <i class="{{ $link['icon'] }} text-sm"></i>
                                    @if(!empty($link['badge']))
                                        <span class="absolute -end-3 -top-2 inline-flex min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold leading-4 text-white">
                                            {{ $link['badge'] }}
                                        </span>
                                    @endif
                                </span>
                                <span class="max-w-full truncate">{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endauth
    </div>

    <x-otp-verify-popup :open="session('show_otp_verify', false)" />

    @stack('scripts')
    @if (filled($sharedBodyScripts ?? ''))
    {!! $sharedBodyScripts !!}
    @endif
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: '{{ app()->getLocale() == "ar" ? "هل أنت متأكد؟" : "Are you sure?" }}',
                text: '{{ app()->getLocale() == "ar" ? "لن تتمكن من التراجع عن هذا!" : "You won\\'t be able to revert this!" }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ app()->getLocale() == "ar" ? "نعم، احذف!" : "Yes, delete it!" }}',
                cancelButtonText: '{{ app()->getLocale() == "ar" ? "إلغاء" : "Cancel" }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
</body>

</html>
