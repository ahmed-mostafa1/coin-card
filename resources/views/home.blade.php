@extends('layouts.app')

@section('title', __('messages.home'))

@section('content')
    @if(isset($activePopups) && $activePopups->isNotEmpty())
        <div x-data="popupManager({{ $activePopups->toJson() }})" x-show="currentPopup" x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">
            
            <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden transform transition-all">
                <button @click="closePopup()" class="absolute top-3 right-3 z-10 p-2 bg-white/80 dark:bg-slate-700/80 rounded-full text-slate-500 hover:text-red-500 transition shadow-sm flex items-center gap-1">
                    <span class="text-sm font-bold px-1">إغلاق</span>
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <template x-if="currentPopup && currentPopup.image_path">
                    <img :src="'/storage/' + currentPopup.image_path" class="w-full h-auto max-h-60 object-cover" :alt="currentPopup.localized_title">
                </template>

                <div class="p-6 text-center">
                    <h3 x-text="currentPopup && currentPopup.localized_title" class="text-xl font-bold text-slate-800 dark:text-white mb-2"></h3>
                    <p x-text="currentPopup && currentPopup.localized_content" class="text-slate-600 dark:text-slate-300 whitespace-pre-wrap"></p>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('popupManager', (popups) => ({
                    popups: popups,
                    currentIndex: 0,
                    currentPopup: null,

                    init() {
                        this.checkPopup();
                    },

                    checkPopup() {
                        if (this.popups.length > 0) {
                            // Check if this specific popup was already closed in this session
                            // Show once per session
                             if (sessionStorage.getItem('popup_closed_' + this.popups[0].id)) return;
                            
                            this.currentPopup = this.popups[this.currentIndex];
                        }
                    },

                    closePopup() {
                        // Mark as seen for this session
                         sessionStorage.setItem('popup_closed_' + this.currentPopup.id, 'true');
                        
                        this.currentPopup = null;
                        
                        // Show next popup if exists
                        this.currentIndex++;
                        if (this.currentIndex < this.popups.length) {
                            setTimeout(() => {
                                this.currentPopup = this.popups[this.currentIndex];
                            }, 500);
                        }
                    },
                    
                    // Helper to get localized text
                    get localized_title() {
                        return this.currentPopup ? ('{{ app()->getLocale() }}' === 'en' && this.currentPopup.title_en ? this.currentPopup.title_en : this.currentPopup.title) : '';
                    },
                    
                    get localized_content() {
                        return this.currentPopup ? ('{{ app()->getLocale() }}' === 'en' && this.currentPopup.content_en ? this.currentPopup.content_en : this.currentPopup.content) : '';
                    }
                }));
            });
        </script>
    @endif

    <div class="store-shell space-y-4 sm:space-y-6">
        <x-store.hero :banners="$sharedBanners" :alt="__('messages.home')" />

        <x-store.notice :text="(app()->getLocale() === 'en' && !empty($sharedTickerTextEn)) ? $sharedTickerTextEn : $sharedTickerText" />

        @if(auth()->check() && !auth()->user()->email_verified_at)
            <div class="w-full px-3 lg:w-4/5 lg:mx-auto">
                <div class="flex items-center justify-between rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        <div>
                            <p class="font-bold">{{ __('messages.account_activation_required') }}</p>
                            <p class="text-sm">{{ __('messages.otp_sent_email_instruction') }}</p>
                        </div>
                    </div>
                    <button @click="$dispatch('open-otp-popup')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600">
                        {{ __('messages.verify') }}
                    </button>
                </div>
            </div>
        @endif

        <div class="w-full px-3 lg:w-4/5 lg:mx-auto">
            <div class="grid gap-2 sm:gap-3 lg:gap-4 grid-cols-2 lg:grid-cols-4" data-filter-list="categories">
                @forelse ($categories as $category)
                    <x-store.category-card :title="$category->localized_name" :href="route('categories.show', $category->slug)"
                        :image="$category->image_path ? asset('storage/' . $category->image_path) : null"
                        searchTarget="categories" />
                @empty
                    <x-empty-state :message="__('messages.no_categories')" class="col-span-2 lg:col-span-4" />
                @endforelse
            </div>
        </div>

        <div class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 pt-2 px-3 text-center text-sm text-slate-700 dark:text-slate-300">
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p1.webp') }}" alt="{{ __('messages.programming_design') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.programming_design') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p2.webp') }}" alt="{{ __('messages.easy_payment') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.easy_payment') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p3.webp') }}" alt="{{ __('messages.fast_reliable') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.fast_reliable') }}</p>
            </div>
            <div class="store-card flex flex-col items-center gap-2 p-4 sm:gap-3 sm:p-6">
                <img src="{{ asset('img/home/p4.webp') }}" alt="{{ __('messages.guarantee') }}" class="h-12 w-12 object-contain sm:h-16 sm:w-16">
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-500 sm:text-sm break-words">{{ __('messages.guarantee') }}</p>
            </div>
        </div>

        <div class="store-card border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 p-4 mx-3 text-base leading-6 text-slate-700 dark:text-slate-300 sm:p-5 sm:text-lg sm:leading-7 break-words">
            {{ (app()->getLocale() === 'en' && !empty($sharedStoreDescriptionEn)) ? $sharedStoreDescriptionEn : $sharedStoreDescription }}
            
            @if(!empty($sharedWhatsappLink))
                <a href="{{ $sharedWhatsappLink }}" target="_blank" class="font-semibold text-orange-700 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300">{{ __('messages.contact_us') }}</a>.
            @else
                <a href="{{ route('about') }}" class="font-semibold text-orange-700 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300">{{ __('messages.contact_us') }}</a>.
            @endif
        </div>
    </div>
@endsection
