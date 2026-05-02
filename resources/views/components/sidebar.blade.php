@props(['open' => false])

<div x-cloak
     x-show="sidebarOpen"
     class="fixed inset-0 z-[60] flex justify-start"
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
         x-transition:enter-start="ltr:-translate-x-full rtl:translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="ltr:-translate-x-full rtl:translate-x-full"
         class="relative flex w-full max-w-xs flex-col overflow-y-auto bg-slate-100 dark:bg-slate-900 shadow-xl transition-all h-full">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-6 border-b border-slate-200 dark:border-slate-800">
             <div class="flex items-center gap-3">
                  @auth
                          @inject('loyaltyService', 'App\Services\LoyaltyService')
                          @php
                              $loyaltyData = $loyaltyService->summary(auth()->user());
                              $progress = $loyaltyData['progress_percent'];
                          @endphp

                    <div class="h-10 w-10 overflow-hidden rounded-full ring-2 ring-emerald-500 bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                        <x-user-badge :user="auth()->user()" class="h-8 w-8" />
                    </div>
                    <div>
                    <!-- <p class="text-xs text-slate-9000 dark:text-slate-50">{{ __('messages.welcome') ?? 'أهلا بك :' }}</p> -->
                          <p class="font-bold text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>

                          <div class="mt-2">
                              <!-- Account loyalty badge -->
                              <div class="flex items-center gap-2 mb-1.5">
                                   <div class="px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider font-bold shadow-sm ring-1 ring-inset bg-emerald-100 text-emerald-900 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800">
                                       <div class="flex items-center gap-1">
                                           <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                           <span>{{ $loyaltyData['level_label'] }}</span>
                                       </div>
                                   </div>
                              </div>
                              
                              <!-- Next Level Progress -->
                              <div class="bg-gray-200 dark:bg-slate-700 rounded-full h-1.5 w-full max-w-[120px] mb-1 overflow-hidden">
                                  <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                              </div>
                              <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">{{ $loyaltyData['next_requirement'] }}</p>
                         </div>
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

        <!-- Language Switcher (Below User Name) -->
        <div class="px-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            @if(app()->getLocale() == 'ar')
                <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-center gap-2 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-language text-emerald-600 dark:text-emerald-400"></i>
                    <span>English</span>
                </a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center justify-center gap-2 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-language text-emerald-600 dark:text-emerald-400"></i>
                    <span>العربية</span>
                </a>
            @endif
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            
            @if(auth()->user()?->hasRole('admin'))
            <a href="{{ route('admin.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-tachometer-alt text-orange-400 w-5"></i>
                <span>{{ __('messages.admin_dashboard') ?? 'Admin Dashboard' }}</span>
            </a>
            @endif

            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-home text-orange-400 w-5"></i>
                <span>{{ __('messages.home') }}</span>
            </a>

            @auth
            <!-- API Docs (Placeholder route) -->
            {{-- <a href="#" class="flex items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-file-code text-orange-400 w-5"></i>
                <span>{{ __('messages.api_docs') ?? 'وثائق API' }}</span>
            </a> --}}

            <a href="{{ route('account') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-user text-orange-400 w-5"></i>
                <span>{{ __('messages.my_account') }}</span>
            </a>

            <a href="{{ route('deposit.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-coins text-orange-400 w-5"></i>
                <span>{{ __('messages.top_up_balance') }}</span>
            </a>

            @if(!auth()->user()?->hasRole('admin'))
            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-shopping-cart text-orange-400 w-5"></i>
                <span>{{ __('messages.my_orders') ?? 'المشتريات' }}</span>
            </a>
            
             <a href="{{ route('account.wallet') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-wallet text-orange-400 w-5"></i>
                <span>{{ __('messages.wallet') ?? 'شحن الرصيد' }}</span>
            </a>
            @endif

            @if(!auth()->user()?->hasRole('admin'))
            <a href="{{ route('agency-requests.create') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-handshake text-orange-400 w-5"></i>
                <span>{{ __('messages.request_agency') ?? (app()->getLocale() == 'ar' ? 'طلب الوكالة' : 'Request Agency') }}</span>
            </a>
            @endif

             <a href="{{ route('account.notifications') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-bell text-orange-400 w-5"></i>
                <span>{{ __('messages.notifications') }}</span>
            </a>
            @endauth

             @if(!auth()->user()?->hasRole('admin'))
             <a href="{{ route('about') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-info-circle text-orange-400 w-5"></i>
                <span>{{ __('messages.about_us') }}</span>
            </a>

            <a href="{{ route('privacy-policy') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-shield-alt text-orange-400 w-5"></i>
                <span>{{ __('messages.privacy_policy') }}</span>
            </a>

            <a href="{{ route('terms-of-use') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-file-contract text-orange-400 w-5"></i>
                <span>{{ __('messages.terms_of_use') }}</span>
            </a>

            @endif

            <a href="{{ route('contact-us.show') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-envelope text-orange-400 w-5"></i>
                <span>{{ __('messages.contact_page') }}</span>
            </a>
            
            @if(($sharedWhatsappEnabled ?? '0') === '1' && filled($sharedWhatsappLink ?? '') && $sharedWhatsappLink !== '#')
             <a href="{{ $sharedWhatsappLink }}" target="_blank" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-brands fa-whatsapp text-green-500 w-5 text-lg"></i>
                <span>{{ __('messages.contact_whatsapp') ?? 'تواصل مع الإدارة' }}</span>
            </a>
            @endif

            @auth
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-arrow-right-from-bracket text-orange-400 w-5 rtl:rotate-180"></i>
                        <span>{{ __('messages.logout') }}</span>
                    </button>
                </form>
            @endauth

        </nav>

        <!-- Footer Socials -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex justify-center gap-6">
                @if(($sharedWhatsappEnabled ?? '0') === '1' && filled($sharedWhatsappLink ?? '') && $sharedWhatsappLink !== '#')
                 <a href="{{ $sharedWhatsappLink }}" target="_blank" class="text-green-500 hover:scale-110 transition">
                    <i class="fa-brands fa-whatsapp text-3xl"></i>
                </a>
                @endif
                @if(($sharedTelegramEnabled ?? '0') === '1' && filled($sharedTelegramLink ?? '') && $sharedTelegramLink !== '#')
                <a href="{{ $sharedTelegramLink }}" target="_blank" class="text-blue-500 hover:scale-110 transition">
                    <i class="fa-brands fa-telegram text-3xl"></i>
                </a>
                @endif
                @if(($sharedFacebookEnabled ?? '0') === '1' && filled($sharedFacebookLink ?? '') && $sharedFacebookLink !== '#')
                <a href="{{ $sharedFacebookLink }}" target="_blank" class="text-blue-700 hover:scale-110 transition">
                    <i class="fa-brands fa-facebook text-3xl"></i>
                </a>
                @endif
                @if(($sharedYoutubeEnabled ?? '0') === '1' && filled($sharedYoutubeLink ?? '') && $sharedYoutubeLink !== '#')
                <a href="{{ $sharedYoutubeLink }}" target="_blank" class="text-red-600 hover:scale-110 transition" title="YouTube">
                    <i class="fa-brands fa-youtube text-3xl"></i>
                </a>
                @endif
                @if(($sharedTiktokEnabled ?? '0') === '1' && filled($sharedTiktokLink ?? '') && $sharedTiktokLink !== '#')
                <a href="{{ $sharedTiktokLink }}" target="_blank" class="text-slate-900 dark:text-white hover:scale-110 transition">
                    <i class="fa-brands fa-tiktok text-3xl"></i>
                </a>
                @endif
            </div>
        </div>

    </div>
</div>
