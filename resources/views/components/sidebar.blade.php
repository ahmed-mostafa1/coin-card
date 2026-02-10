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
                         @inject('vipService', 'App\Services\VipService')
                         @php
                             $vipData = $vipService->getVipSummary(auth()->user());
                             $currentTier = $vipData['current_tier'];
                             $nextTier = $vipData['next_tier'];
                             $remaining = $vipData['remaining_to_next'];
                             $progress = $vipData['progress_percent'];
                             
                             // Tier specific styling based on rank or default
                             $tierStyles = [
                                 0 => 'bg-slate-200 text-slate-600 ring-slate-300 dark:bg-slate-700 dark:text-slate-300 dark:ring-slate-600', // Basic/Member
                                 1 => 'bg-slate-500 text-white ring-slate-300', // Silver/Basic
                                 2 => 'bg-yellow-500 text-white ring-yellow-300', // Gold
                                 3 => 'bg-emerald-500 text-white ring-emerald-300', // Platinum/Emerald
                                 4 => 'bg-purple-600 text-white ring-purple-400', // Diamond
                                 5 => 'bg-rose-600 text-white ring-rose-300', // Mythic
                             ];
                             
                             $rank = $currentTier?->rank ?? 0;
                             $currentStyle = $tierStyles[$rank] ?? $tierStyles[1];
                         @endphp

                    <div class="h-10 w-10 overflow-hidden rounded-full ring-2 ring-emerald-500 bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                         @php
                             $hasTierImage = $currentTier ? ($currentTier->image_path ? true : false) : false;
                         @endphp
                         @if($hasTierImage)
                             <img src="{{ asset('storage/' . $currentTier->image_path) }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
                         @else
                            <img src="{{ asset('img/vip0.png') }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
                         @endif
                    </div>
                    <div>
                    <!-- <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('messages.welcome') ?? 'أهلا بك :' }}</p> -->
                         <p class="font-bold text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>

                         <div class="mt-2">
                             <!-- VIP Badge -->
                             <div class="flex items-center gap-2 mb-1.5">
                                  <div class="px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider font-bold shadow-sm ring-1 ring-inset {{ $currentStyle }}">
                                      <div class="flex items-center gap-1">
                                          @php
                                              $hasBadgeImage = $currentTier ? ($currentTier->image_path ? true : false) : false;
                                          @endphp
                                          @if($hasBadgeImage)
                                            <img src="{{ asset('storage/' . $currentTier->image_path) }}" alt="VIP" class="w-3 h-3 object-contain invert brightness-0">
                                          @elseif($currentTier)
                                            <i class="fa-solid fa-crown text-[10px]"></i>
                                          @else
                                            <i class="fa-solid fa-user text-[10px]"></i>
                                          @endif
                                          <span>
                                              @if($currentTier)
                                                  {{ app()->getLocale() == 'ar' ? $currentTier->title_ar : $currentTier->title_en }}
                                              @else
                                                  {{ __('messages.member') ?? (app()->getLocale() == 'ar' ? 'عضو' : 'Member') }}
                                              @endif
                                          </span>
                                      </div>
                                  </div>
                             </div>
                             
                             <!-- Next Level Progress -->
                             @if($nextTier)
                                 <div class="bg-gray-200 dark:bg-slate-700 rounded-full h-1.5 w-full max-w-[120px] mb-1 overflow-hidden">
                                     <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                 </div>
                                 <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">
                                     {{ __('messages.remaining') ?? (app()->getLocale() == 'ar' ? 'باقي' : 'Remaining') }}: 
                                     <span class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($remaining, 2) }}</span>
                                     {{ __('messages.currency') ?? '$' }} 
                                     {{ __('messages.to_reach') ?? (app()->getLocale() == 'ar' ? 'للوصول إلى' : 'to reach') }}
                                     <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ app()->getLocale() == 'ar' ? $nextTier->title_ar : $nextTier->title_en }}</span>
                                 </p>
                             @else
                                 <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">
                                     {{ __('messages.max_level') ?? (app()->getLocale() == 'ar' ? 'وصلت لأعلى مستوى!' : 'Max Level Reached!') }} <i class="fa-solid fa-star text-yellow-400"></i>
                                 </p>
                             @endif
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
                <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-center gap-2 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-language text-emerald-600 dark:text-emerald-400"></i>
                    <span>English</span>
                </a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center justify-center gap-2 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-language text-emerald-600 dark:text-emerald-400"></i>
                    <span>العربية</span>
                </a>
            @endif
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

            @if(!auth()->user()?->hasRole('admin'))
            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-shopping-cart text-orange-400 w-5"></i>
                <span>{{ __('messages.my_orders') ?? 'المشتريات' }}</span>
            </a>
            
             <a href="{{ route('account.wallet') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-wallet text-orange-400 w-5"></i>
                <span>{{ __('messages.wallet') ?? 'شحن الرصيد' }}</span>
            </a>

            <a href="{{ route('deposit.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-coins text-orange-400 w-5"></i>
                <span>{{ __('messages.top_up_balance') }}</span>
            </a>
            @endif

            @if(!auth()->user()?->hasRole('admin'))
            <a href="{{ route('agency-requests.create') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-handshake text-orange-400 w-5"></i>
                <span>{{ __('messages.request_agency') ?? (app()->getLocale() == 'ar' ? 'طلب الوكالة' : 'Request Agency') }}</span>
            </a>
            @endif

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

             @if(!auth()->user()?->hasRole('admin'))
             <a href="{{ route('about') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-info-circle text-orange-400 w-5"></i>
                <span>{{ __('messages.about_us') }}</span>
            </a>

            <a href="{{ route('privacy-policy') }}" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-shield-alt text-orange-400 w-5"></i>
                <span>{{ __('messages.privacy_policy') }}</span>
            </a>
            @endif
            
            @if($sharedWhatsappLink)
             <a href="{{ $sharedWhatsappLink }}" target="_blank" class="flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-brands fa-whatsapp text-green-500 w-5 text-lg"></i>
                <span>{{ __('messages.contact_whatsapp') ?? 'تواصل مع الإدارة' }}</span>
            </a>
            @endif

            @auth
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-lg border border-slate-400 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-arrow-right-from-bracket text-orange-400 w-5 rtl:rotate-180"></i>
                        <span>{{ __('messages.logout') }}</span>
                    </button>
                </form>
            @endauth

        </nav>

        <!-- Footer Socials -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex justify-center gap-6">
                @if($sharedWhatsappLink)
                 <a href="{{ $sharedWhatsappLink }}" target="_blank" class="text-green-500 hover:scale-110 transition">
                    <i class="fa-brands fa-whatsapp text-3xl"></i>
                </a>
                @endif
                @if($sharedInstagramLink)
                <a href="{{ $sharedInstagramLink }}" target="_blank" class="text-pink-600 hover:scale-110 transition">
                    <i class="fa-brands fa-instagram text-3xl"></i>
                </a>
                @endif
                @if($sharedTelegramLink)
                <a href="{{ $sharedTelegramLink }}" target="_blank" class="text-blue-500 hover:scale-110 transition">
                    <i class="fa-brands fa-telegram text-3xl"></i>
                </a>
                @endif
                @if($sharedFacebookLink)
                <a href="{{ $sharedFacebookLink }}" target="_blank" class="text-blue-700 hover:scale-110 transition">
                    <i class="fa-brands fa-facebook text-3xl"></i>
                </a>
                @endif
                <a href="{{ $sharedUpscrollLink }}" target="_blank" class="text-slate-600 dark:text-slate-400 hover:scale-110 transition" title="UpScrolled">
                    <img src="{{ asset('img/upscrolled.png') }}" alt="UpScrolled" class="w-8 h-8">
                </a>
            </div>
        </div>

    </div>
</div>
