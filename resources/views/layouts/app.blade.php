<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Arab 8BP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        // Initialize theme before page renders to prevent flash
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-200" 
      x-data="{ 
          sidebarOpen: false, 
          darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="$watch('darkMode', val => {
          localStorage.theme = val ? 'dark' : 'light';
          if(val) document.documentElement.classList.add('dark');
          else document.documentElement.classList.remove('dark');
      })">

    @php
        $containerWidth = 'w-[95%] md:w-[80%] mx-auto';
        $mainWidth = trim($__env->yieldContent('mainWidth', $containerWidth));
        $isAr = app()->getLocale() == 'ar';
    @endphp

    <div class="min-h-screen flex flex-col">
        <!-- Fixed Navbar - Mobile Optimized -->
        <nav class="border-b border-slate-200 dark:border-slate-700 bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm transition-colors duration-200 sticky top-0 z-50">
            
            <!-- MOBILE LAYOUT: Menu (Start), Logo (Center), Balance (End) -->
            <div class="flex md:hidden h-14 items-center justify-between px-3">
                
                <!-- START: Theme + Menu -->
                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" @click="sidebarOpen = true" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700 transition">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <button type="button" @click="darkMode = !darkMode" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 transition">
                        <i class="fa-solid fa-sun text-sm" x-show="!darkMode" x-cloak></i>
                        <i class="fa-solid fa-moon text-sm" x-show="darkMode" x-cloak></i>
                    </button>
                </div>
                
                <!-- CENTER: Logo -->
                <div class="flex-1 flex justify-center px-2">
                    <a href="{{ route('home') }}" class="min-w-0 max-w-full flex items-center justify-center">
                        @if($sharedLogoType === 'image' && $sharedLogoImage)
                            <img src="{{ asset('storage/' . $sharedLogoImage) }}" alt="Logo" class="h-8 object-contain">
                        @else
                            <span class="truncate text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ $sharedLogoText }}</span>
                        @endif
                    </a>
                </div>

                <!-- END: Balance Badge -->
                <div class="shrink-0">
                    @auth
                        <a href="{{ route('account.wallet') }}" class="flex items-center gap-1 rounded-full border border-orange-200 bg-orange-50 px-2 py-1 transition hover:bg-orange-100 dark:border-orange-900/50 dark:bg-orange-900/20 dark:hover:bg-orange-900/30">
                             <i class="fa-solid fa-wallet text-orange-500 text-xs"></i>
                            <span class="text-xs font-bold text-orange-600 dark:text-orange-500 whitespace-nowrap" dir="ltr">{{ number_format(auth()->user()->available_balance, 2) }} $</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-700">
                            {{ __('messages.login') }}
                        </a>
                    @endauth
                </div>
            </div>

            <!-- DESKTOP LAYOUT: Keep existing design -->
            <div class="hidden md:flex h-14 items-center justify-between gap-1.5 px-2.5 sm:gap-2 sm:px-3">
                
                <!-- Left Group: Menu Button + Theme Toggle (shrink-0) -->
                <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                    <button type="button" @click="sidebarOpen = true" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700 transition">
                        <i class="fa-solid fa-bars text-lg sm:text-xl"></i>
                    </button>

                    <!-- Theme Toggle (always visible) -->
                    <button type="button" @click="darkMode = !darkMode" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 transition">
                        <i class="fa-solid fa-sun text-sm sm:text-base" x-show="!darkMode" x-cloak></i>
                        <i class="fa-solid fa-moon text-sm sm:text-base" x-show="darkMode" x-cloak></i>
                    </button>
                </div>

                <!-- Center: Logo (min-w-0 allows truncation) -->
                <div class="flex min-w-0 flex-1 items-center justify-center px-1 sm:px-2">
                    <a href="{{ route('home') }}" class="min-w-0 max-w-full flex items-center justify-center">
                        @if($sharedLogoType === 'image' && $sharedLogoImage)
                            <img src="{{ asset('storage/' . $sharedLogoImage) }}" alt="Logo" class="h-8 sm:h-10 lg:h-12 object-contain">
                        @else
                            <span class="truncate text-sm font-bold text-emerald-700 dark:text-emerald-400 sm:text-base lg:text-lg">{{ $sharedLogoText }}</span>
                        @endif
                    </a>
                </div>
                
                <!-- Right Group: Balance (shrink-0) -->
                <div class="flex shrink-0 items-center">
                    @auth
                        <a href="{{ route('account.wallet') }}" class="flex shrink-0 items-center gap-1 rounded-full border border-orange-200 bg-orange-50 px-1.5 py-0.5 transition hover:bg-orange-100 dark:border-orange-900/50 dark:bg-orange-900/20 dark:hover:bg-orange-900/30 sm:gap-1.5 sm:px-2 sm:py-1 lg:gap-2 lg:px-3 lg:py-1.5">
                            <i class="fa-solid fa-wallet text-orange-500 text-[10px] sm:text-xs lg:text-sm"></i>
                            <span class="text-[10px] font-bold text-orange-600 dark:text-orange-500 sm:text-xs lg:text-sm whitespace-nowrap" dir="ltr">{{ number_format(auth()->user()->available_balance, 2) }} $</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="flex shrink-0 items-center rounded-full bg-emerald-600 px-2.5 py-1 text-[10px] font-semibold text-white hover:bg-emerald-700 sm:px-3 sm:py-1.5 sm:text-xs lg:px-4 lg:text-sm">
                            {{ __('messages.login') }}
                        </a>
                    @endauth
                </div>
                
            </div>

        </nav>

        <main class="flex-1 py-4 pb-32 sm:py-6 md:pb-28 {{ $mainWidth }}">
            @yield('content')
        </main>

        <!-- New Sticky Footer -->
        <footer class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] transition-colors duration-200">
             <!-- Desktop Layout -->
            <div class="hidden md:flex h-20 items-center px-4">
                <div class="flex items-center justify-between w-full gap-6 text-slate-500 dark:text-slate-400">
                    <!-- Orders/Cart -->
                    <a href="{{ route('account.orders') }}" 
                       class="{{ request()->routeIs('account.orders*') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white dark:ring-slate-800 scale-110' : 'bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-800 dark:hover:text-slate-200' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-basket-shopping text-xl"></i>
                    </a>

                    <!-- Notifications -->
                    <a href="{{ route('account.notifications') }}" 
                       class="{{ request()->routeIs('account.notifications*') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white dark:ring-slate-800 scale-110' : 'bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-800 dark:hover:text-slate-200' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-bell text-xl"></i>
                        @if (!empty($navUnreadCount ?? 0))
                            <span class="absolute -top-1 -end-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] text-white">{{ $navUnreadCount }}</span>
                        @endif
                    </a>

                    <!-- Home (Center) -->
                    <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-1 group">
                         <div class="{{ request()->routeIs('home') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white dark:ring-slate-800 scale-110' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-800 dark:hover:text-slate-200' }} flex h-12 w-12 items-center justify-center rounded-full transition group-hover:scale-110">
                            <i class="fa-solid fa-home text-xl"></i>
                         </div>
                         <span class="{{ request()->routeIs('home') ? 'text-orange-500' : 'text-slate-500 dark:text-slate-400' }} text-xs font-bold">{{ __('messages.home') }}</span>
                    </a>

                    <!-- Balance -->
                    <a href="{{ route('account.wallet') }}" 
                        class="{{ request()->routeIs('account.wallet*') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white dark:ring-slate-800 scale-110' : 'bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-800 dark:hover:text-slate-200' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </a>

                    <!-- Account -->
                    <a href="{{ route('account') }}" 
                        class="{{ request()->routeIs('account') ? 'bg-orange-500 text-white shadow-lg ring-4 ring-white dark:ring-slate-800 scale-110' : 'bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-800 dark:hover:text-slate-200' }} group relative flex h-10 w-10 items-center justify-center rounded-full transition">
                        <i class="fa-solid fa-user text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Mobile Layout - FLAT DESIGN -->
             <div class="flex md:hidden h-16 w-full items-center pb-[env(safe-area-inset-bottom)]">
                 <div class="flex items-center justify-around w-full px-2">
                     
                     <!-- Orders -->
                     <a href="{{ route('account.orders') }}" class="flex flex-col items-center gap-1">
                         @if(request()->routeIs('account.orders*'))
                             <div class="bg-orange-500 rounded-full p-2.5">
                                 <i class="fa-solid fa-basket-shopping text-sm text-white"></i>
                             </div>
                             <span class="text-[9px] font-bold text-orange-500">Orders</span>
                         @else
                             <i class="fa-solid fa-basket-shopping text-lg text-slate-500 dark:text-slate-400"></i>
                         @endif
                     </a>
                     
                     <!-- Notifications -->
                     <a href="{{ route('account.notifications') }}" class="relative flex flex-col items-center gap-1">
                         @if(request()->routeIs('account.notifications*'))
                             <div class="bg-orange-500 rounded-full p-2.5">
                                 <i class="fa-solid fa-bell text-sm text-white"></i>
                             </div>
                             <span class="text-[9px] font-bold text-orange-500">Alerts</span>
                         @else
                             <i class="fa-solid fa-bell text-lg text-slate-500 dark:text-slate-400"></i>
                             @if (!empty($navUnreadCount ?? 0))
                                 <span class="absolute top-0 end-0 flex h-3 w-3 items-center justify-center rounded-full bg-rose-500 text-[8px] text-white">{{ $navUnreadCount }}</span>
                             @endif
                         @endif
                     </a>
                     
                     <!-- Home (Center) -->
                     <a href="{{ route('home') }}" class="flex flex-col items-center gap-1">
                         @if(request()->routeIs('home'))
                             <div class="bg-orange-500 rounded-full p-2.5">
                                 <i class="fa-solid fa-home text-sm text-white"></i>
                             </div>
                             <span class="text-[9px] font-bold text-orange-500">{{ __('messages.home') }}</span>
                         @else
                             <i class="fa-solid fa-home text-lg text-slate-500 dark:text-slate-400"></i>
                         @endif
                     </a>
                     
                     <!-- Balance -->
                     <a href="{{ route('account.wallet') }}" class="flex flex-col items-center gap-1">
                         @if(request()->routeIs('account.wallet*'))
                             <div class="bg-orange-500 rounded-full p-2.5">
                                 <i class="fa-solid fa-wallet text-sm text-white"></i>
                             </div>
                             <span class="text-[9px] font-bold text-orange-500">Balance</span>
                         @else
                             <i class="fa-solid fa-wallet text-lg text-slate-500 dark:text-slate-400"></i>
                         @endif
                     </a>
                     
                     <!-- Account -->
                     <a href="{{ route('account') }}" class="flex flex-col items-center gap-1">
                         @if(request()->routeIs('account') && !request()->routeIs('account.orders*') && !request()->routeIs('account.notifications*') && !request()->routeIs('account.wallet*'))
                             <div class="bg-orange-500 rounded-full p-2.5">
                                 <i class="fa-solid fa-user text-sm text-white"></i>
                             </div>
                             <span class="text-[9px] font-bold text-orange-500">Account</span>
                         @else
                             <i class="fa-solid fa-user text-lg text-slate-500 dark:text-slate-400"></i>
                         @endif
                     </a>
                     
                 </div>
             </div>
        </footer>
    </div>

    <!-- Sidebar Component -->
    <x-sidebar />

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    {{-- Script tag removed since functionality is now in Alpine x-data --}}
    <x-otp-verify-popup :open="session('show_otp_verify', false)" />

    @stack('scripts')
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: '{{ app()->getLocale() == "ar" ? "هل أنت متأكد؟" : "Are you sure?" }}',
                text: '{{ app()->getLocale() == "ar" ? "لن تتمكن من التراجع عن هذا!" : "You won'\''t be able to revert this!" }}',
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
