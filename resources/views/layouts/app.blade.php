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
        $containerWidth = 'w-[95%] md:w-[80%] mx-auto';
        $mainWidth = trim($__env->yieldContent('mainWidth', $containerWidth));
        $isAr = app()->getLocale() == 'ar';
        $currentUser = auth()->user();
        $isAdmin = $currentUser?->hasRole('admin') ?? false;
        $isAccountOverviewActive = request()->routeIs('account')
            && !request()->routeIs('account.orders*')
            && !request()->routeIs('account.notifications*')
            && !request()->routeIs('account.wallet*');
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

            $primaryNavLinks = array_values(array_filter([
                [
                    'key' => 'admin',
                    'route' => route('admin.index'),
                    'label' => __('messages.admin_dashboard'),
                    'icon' => 'fa-solid fa-gauge-high',
                    'active' => request()->routeIs('admin.*'),
                    'show' => $isAdmin,
                ],
                [
                    'key' => 'home',
                    'route' => route('home'),
                    'label' => __('messages.home'),
                    'icon' => 'fa-solid fa-house',
                    'active' => request()->routeIs('home'),
                    'show' => true,
                ],
                [
                    'key' => 'account',
                    'route' => route('account'),
                    'label' => __('messages.my_account'),
                    'icon' => 'fa-solid fa-user',
                    'active' => $isAccountOverviewActive,
                    'show' => true,
                ],
                [
                    'key' => 'deposit',
                    'route' => route('deposit.index'),
                    'label' => __('messages.top_up_balance'),
                    'icon' => 'fa-solid fa-plus-circle',
                    'active' => request()->routeIs('deposit.*'),
                    'show' => true,
                ],
                [
                    'key' => 'orders',
                    'route' => route('account.orders'),
                    'label' => __('messages.my_orders'),
                    'icon' => 'fa-solid fa-basket-shopping',
                    'active' => request()->routeIs('account.orders*'),
                    'show' => ! $isAdmin,
                ],
                [
                    'key' => 'wallet',
                    'route' => route('account.wallet'),
                    'label' => __('messages.wallet'),
                    'icon' => 'fa-solid fa-wallet',
                    'active' => request()->routeIs('account.wallet*'),
                    'show' => ! $isAdmin,
                ],
                [
                    'key' => 'notifications',
                    'route' => route('account.notifications'),
                    'label' => __('messages.notifications'),
                    'icon' => 'fa-solid fa-bell',
                    'active' => request()->routeIs('account.notifications*'),
                    'show' => true,
                    'badge' => $navUnreadCount ?? 0,
                ],
                [
                    'key' => 'agency',
                    'route' => route('agency-requests.create'),
                    'label' => __('messages.request_agency'),
                    'icon' => 'fa-solid fa-handshake',
                    'active' => request()->routeIs('agency-requests.*'),
                    'show' => ! $isAdmin,
                ],
            ], fn ($link) => $link['show']));

            $desktopPrimaryNavLinks = array_values(array_filter(
                $primaryNavLinks,
                fn ($link) => ! in_array($link['key'], ['account', 'notifications'], true)
            ));

            $mobilePriorityKeys = $isAdmin
                ? ['admin', 'home', 'notifications', 'account']
                : ['home', 'deposit', 'orders', 'notifications', 'account'];

            $mobilePrimaryNavLinks = array_values(array_filter(
                $primaryNavLinks,
                fn ($link) => in_array($link['key'], $mobilePriorityKeys, true)
            ));

            usort(
                $mobilePrimaryNavLinks,
                fn ($a, $b) => array_search($a['key'], $mobilePriorityKeys, true) <=> array_search($b['key'], $mobilePriorityKeys, true)
            );

            $profileQuickLinks = array_values(array_filter([
                [
                    'route' => route('account'),
                    'label' => __('messages.my_account'),
                    'icon' => 'fa-solid fa-id-card',
                    'show' => true,
                ],
                [
                    'route' => route('account.vip'),
                    'label' => __('messages.vip_title'),
                    'icon' => 'fa-solid fa-crown',
                    'show' => true,
                ],
                [
                    'route' => route('account.wallet'),
                    'label' => __('messages.wallet_history'),
                    'icon' => 'fa-solid fa-wallet',
                    'show' => ! $isAdmin,
                ],
                [
                    'route' => route('account.orders'),
                    'label' => __('messages.my_orders'),
                    'icon' => 'fa-solid fa-basket-shopping',
                    'show' => ! $isAdmin,
                ],
                [
                    'route' => route('account.notifications'),
                    'label' => __('messages.notifications'),
                    'icon' => 'fa-solid fa-bell',
                    'show' => true,
                    'badge' => $navUnreadCount ?? 0,
                ],
                [
                    'route' => route('admin.index'),
                    'label' => __('messages.admin_dashboard'),
                    'icon' => 'fa-solid fa-gauge-high',
                    'show' => $isAdmin,
                ],
            ], fn ($link) => $link['show']));
        @endphp
    @endauth

    <div class="min-h-screen flex flex-col">
        <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-sm transition-colors duration-200 dark:border-slate-700 dark:bg-slate-800/95">
            <div class="px-3 py-3 sm:px-4">
                <div class="flex items-center justify-between gap-2 lg:gap-3">
                    <a href="{{ route('home') }}"
                       class="flex min-w-0 shrink items-center gap-2.5 rounded-2xl border border-emerald-200 bg-white px-3 py-2 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 dark:border-emerald-900/70 dark:bg-slate-900 dark:hover:bg-emerald-950/30 sm:gap-3 sm:px-4 sm:py-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-sm sm:h-10 sm:w-10">
                            <i class="fa-solid fa-credit-card text-sm"></i>
                        </span>
                        <span class="min-w-0 max-w-[8.75rem] sm:max-w-none">
                            <span class="block text-sm font-black tracking-wide text-slate-900 dark:text-white">شام كاش</span>
                            <span class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ $isAr ? 'خدمات رقمية سريعة' : 'Fast Digital Services' }}</span>
                        </span>
                    </a>

                    @auth
                        <div class="hidden min-w-0 flex-1 items-center justify-center lg:flex">
                            <div class="flex items-center gap-2 overflow-x-auto px-2 pb-1 scrollbar-none" style="-ms-overflow-style:none;scrollbar-width:none;">
                                @foreach($desktopPrimaryNavLinks as $link)
                                    <a href="{{ $link['route'] }}"
                                       class="inline-flex shrink-0 items-center gap-2 rounded-full border px-4 py-2.5 text-sm font-semibold transition {{ $link['active'] ? 'border-emerald-300 bg-emerald-600 text-white shadow-sm dark:border-emerald-500 dark:bg-emerald-500' : 'border-slate-200 bg-white text-slate-700 hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300' }}">
                                        <i class="{{ $link['icon'] }} text-sm"></i>
                                        <span>{{ $link['label'] }}</span>
                                        @if(!empty($link['badge']))
                                            <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ $link['badge'] }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endauth

                    <div class="ms-auto flex shrink-0 items-center gap-2">
                        @auth
                            <a href="{{ route('account.notifications') }}"
                               class="relative hidden h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-emerald-700 dark:hover:text-emerald-300 lg:flex"
                               aria-label="{{ __('messages.notifications') }}">
                                <i class="fa-solid fa-bell text-sm"></i>
                                @if(($navUnreadCount ?? 0) > 0)
                                    <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ min($navUnreadCount, 99) }}</span>
                                @endif
                            </a>
                        @endauth

                        <button type="button" @click="darkMode = !darkMode"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-sun text-sm" x-show="!darkMode" x-cloak></i>
                            <i class="fa-solid fa-moon text-sm" x-show="darkMode" x-cloak></i>
                        </button>

                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button type="button"
                                        @click="open = !open"
                                        :aria-expanded="open.toString()"
                                        class="flex items-center gap-0 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-1.5 py-1.5 text-right shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100/80 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:hover:bg-emerald-950/50 sm:gap-3 sm:px-2.5 sm:py-2">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-600 text-sm font-black text-white ring-2 ring-white dark:ring-slate-900">
                                        @if($profileAvatar)
                                            <img src="{{ $profileAvatar }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ $profileInitial }}
                                        @endif
                                    </span>
                                    <span class="hidden min-w-0 lg:block">
                                        <span class="block truncate text-sm font-bold text-slate-900 dark:text-white">{{ $currentUser->name }}</span>
                                        <span class="mt-1 inline-flex max-w-full items-center gap-1 rounded-full bg-white/80 px-2 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-slate-900/70 dark:text-emerald-300">
                                            <i class="fa-solid fa-crown text-[10px] text-amber-400"></i>
                                            <span class="truncate">{{ $menuTierLabel }}</span>
                                        </span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down hidden text-xs text-slate-400 transition-transform lg:block" :class="{ 'rotate-180': open }"></i>
                                </button>

                                <div x-show="open"
                                     x-cloak
                                     x-transition.origin.top.right
                                     @click.outside="open = false"
                                     @keydown.escape.window="open = false"
                                     class="absolute end-0 top-full z-50 mt-3 w-[min(22rem,calc(100vw-1rem))] overflow-hidden rounded-3xl border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 sm:w-[min(24rem,calc(100vw-1.5rem))]">
                                    <div class="flex items-start gap-3">
                                        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-emerald-600 text-lg font-black text-white">
                                            @if($profileAvatar)
                                                <img src="{{ $profileAvatar }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                                            @else
                                                {{ $profileInitial }}
                                            @endif
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-base font-bold text-slate-900 dark:text-white">{{ $currentUser->name }}</p>
                                            <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $currentUser->email }}</p>
                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                                    <i class="fa-solid fa-crown text-[11px] text-amber-400"></i>
                                                    {{ $menuTierLabel }}
                                                </span>
                                                @if(($navUnreadCount ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-950/40 dark:text-rose-300">
                                                        <i class="fa-solid fa-bell text-[11px]"></i>
                                                        {{ $navUnreadCount }} {{ $isAr ? 'غير مقروء' : 'Unread' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/80">
                                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ __('messages.available_balance') }}</span>
                                            <p class="mt-1 text-sm font-bold text-emerald-700 dark:text-emerald-300" dir="ltr">$ {{ number_format($currentUser->available_balance, 2) }}</p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/80">
                                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ __('messages.held_balance') }}</span>
                                            <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white" dir="ltr">$ {{ number_format($currentUser->wallet?->held_balance ?? 0, 2) }}</p>
                                        </div>
                                    </div>

                                    @if($menuNextTier)
                                        <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50/70 px-3 py-3 dark:border-emerald-900/50 dark:bg-emerald-950/20">
                                            <div class="flex items-center justify-between gap-3 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                                <span>{{ $isAr ? 'التقدم للمستوى التالي' : 'Next VIP Progress' }}</span>
                                                <span>{{ round($menuProgress) }}%</span>
                                            </div>
                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-emerald-100 dark:bg-emerald-900/70">
                                                <div class="h-full rounded-full bg-amber-400 transition-all" style="width: {{ $menuProgress }}%"></div>
                                            </div>
                                            <p class="mt-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                                {{ $isAr ? 'المستوى التالي:' : 'Next rank:' }} {{ $isAr ? $menuNextTier->title_ar : ($menuNextTier->title_en ?: $menuNextTier->title_ar) }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        @foreach($profileQuickLinks as $link)
                                            <a href="{{ $link['route'] }}"
                                               class="flex items-center justify-between rounded-2xl border border-slate-200 px-3 py-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300">
                                                <span class="flex min-w-0 items-center gap-2">
                                                    <i class="{{ $link['icon'] }} text-sm shrink-0"></i>
                                                    <span class="truncate">{{ $link['label'] }}</span>
                                                </span>
                                                @if(!empty($link['badge']))
                                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ $link['badge'] }}</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>

                                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                                        @csrf
                                        <button type="submit"
                                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                                            <span>{{ __('messages.logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                               class="flex items-center rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                {{ __('messages.login') }}
                            </a>
                        @endauth
                    </div>
                </div>

                @auth
                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1 scrollbar-none lg:hidden" style="-ms-overflow-style:none;scrollbar-width:none;">
                        @foreach($mobilePrimaryNavLinks as $link)
                            <a href="{{ $link['route'] }}"
                               class="inline-flex shrink-0 items-center gap-2 rounded-2xl border px-4 py-2.5 text-sm font-semibold transition {{ $link['active'] ? 'border-emerald-300 bg-emerald-600 text-white shadow-sm dark:border-emerald-500 dark:bg-emerald-500' : 'border-slate-200 bg-white text-slate-700 hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300' }}">
                                <span class="flex min-w-0 items-center gap-2">
                                    <i class="{{ $link['icon'] }} text-sm shrink-0"></i>
                                    <span class="truncate">{{ $link['label'] }}</span>
                                </span>
                                @if(!empty($link['badge']))
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ $link['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endauth
            </div>
        </nav>

        <main class="flex-1 py-4 sm:py-6 {{ $mainWidth }}">
            @yield('content')
        </main>
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
