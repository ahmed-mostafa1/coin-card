@props(['open' => false])

<div x-cloak
     x-show="sidebarOpen"
     class="fixed inset-0 z-[60] flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }}"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Sidebar Panel -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="relative flex w-full max-w-xs flex-col overflow-y-auto bg-slate-100 dark:bg-slate-900 shadow-xl transition-all h-full">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-6 border-b border-slate-200 dark:border-slate-800">
             <div class="flex items-center gap-3">
                 @auth
                    <div class="h-10 w-10 overflow-hidden rounded-full ring-2 ring-emerald-500 bg-slate-200 dark:bg-slate-700">
                         <!-- Placeholder for user avatar or initial -->
                         <div class="flex h-full w-full items-center justify-center text-slate-500 dark:text-slate-300 font-bold">
                             {{ substr(auth()->user()->name, 0, 1) }}
                         </div>
                    </div>
                    <div>
                         <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('messages.welcome') ?? 'أهلا بك :' }}</p>
                         <p class="font-bold text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>
                    </div>
                 @else
                    <a href="{{ route('login') }}" class="font-bold text-slate-800 dark:text-white">{{ __('messages.login') }}</a>
                 @endauth
             </div>
            <button @click="sidebarOpen = false" type="button" class="rounded-md p-2 text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span class="sr-only">Close sidebar</span>
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            
            @if(auth()->user()?->hasRole('admin'))
            <a href="{{ route('admin.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-tachometer-alt text-orange-400 w-5"></i>
                <span>{{ __('messages.admin_dashboard') ?? 'Admin Dashboard' }}</span>
            </a>
            @endif

            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-home text-orange-400 w-5"></i>
                <span>{{ __('messages.home') }}</span>
            </a>

            @auth
            <!-- API Docs (Placeholder route) -->
            {{-- <a href="#" class="flex items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-file-code text-orange-400 w-5"></i>
                <span>{{ __('messages.api_docs') ?? 'وثائق API' }}</span>
            </a> --}}

            <a href="{{ route('account') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-user text-orange-400 w-5"></i>
                <span>{{ __('messages.my_account') }}</span>
            </a>

            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-shopping-cart text-orange-400 w-5"></i>
                <span>{{ __('messages.my_orders') ?? 'المشتريات' }}</span>
            </a>
            
             <a href="{{ route('account.wallet') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-wallet text-orange-400 w-5"></i>
                <span>{{ __('messages.wallet') ?? 'شحن الرصيد' }}</span>
            </a>

            <!-- Other links based on image/routes -->
            {{-- <a href="#" class="flex items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-money-bill-transfer text-orange-400 w-5"></i>
                <span>{{ __('messages.earnings') ?? 'الأرباح' }}</span>
            </a> --}}
            
             <a href="{{ route('account.notifications') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-bell text-orange-400 w-5"></i>
                <span>{{ __('messages.notifications') }}</span>
            </a>
            @endauth

             <a href="{{ route('about') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-info-circle text-orange-400 w-5"></i>
                <span>{{ __('messages.about_us') }}</span>
            </a>

            <a href="{{ route('privacy-policy') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-shield-alt text-orange-400 w-5"></i>
                <span>{{ __('messages.privacy_policy') }}</span>
            </a>
            
             <a href="https://wa.me/963991195136" target="_blank" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-brands fa-whatsapp text-green-500 w-5 text-lg"></i>
                <span>{{ __('messages.contact_whatsapp') ?? 'تواصل مع الإدارة' }}</span>
            </a>

            @auth
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-arrow-right-from-bracket text-orange-400 w-5"></i>
                        <span>{{ __('messages.logout') }}</span>
                    </button>
                </form>
            @endauth

        </nav>

        <!-- Footer Socials -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex justify-center gap-6">
                 <a href="https://wa.me/963991195136" target="_blank" class="text-green-500 hover:scale-110 transition">
                    <i class="fa-brands fa-whatsapp text-3xl"></i>
                </a>
                <a href="#" class="text-pink-600 hover:scale-110 transition">
                    <i class="fa-brands fa-instagram text-3xl"></i>
                </a>
                <a href="#" class="text-blue-500 hover:scale-110 transition">
                    <i class="fa-brands fa-telegram text-3xl"></i>
                </a>
                <a href="#" class="text-blue-700 hover:scale-110 transition">
                    <i class="fa-brands fa-facebook text-3xl"></i>
                </a>
            </div>
        </div>

    </div>
</div>
