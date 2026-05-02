@extends('layouts.app')

@php
    $homeTitle = app()->getLocale() === 'ar'
        ? 'شحن ألعاب وتطبيقات'
        : 'Games and Apps topup';
    $homeDescriptionSource = app()->getLocale() === 'en' && ! empty($sharedStoreDescriptionEn)
        ? $sharedStoreDescriptionEn
        : $sharedStoreDescription;
    $homeDescription = \Illuminate\Support\Str::limit(
        trim(strip_tags($homeDescriptionSource ?: $homeTitle)),
        160,
        ''
    );
    $heroBanner = $sharedBanners->first();
    $homeImage = $heroBanner?->image_path
        ? asset('storage/' . $heroBanner->image_path)
        : asset('img/placeholder-banner.jpg');
    $homeSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $homeTitle,
        'url' => route('home'),
        'description' => $homeDescription,
        'inLanguage' => app()->getLocale(),
    ];
    $categoryCount = $categories->count();
    $homeFeatureCards = collect($homeFeatureCards ?? []);
    $heroSlides = $sharedBanners
        ->filter(fn ($banner) => filled($banner->image_path))
        ->values()
        ->map(fn ($banner) => [
            'image' => asset('storage/' . $banner->image_path),
            'title' => $banner->localized_title ?: __('messages.home'),
        ])
        ->all();
    $firstHeroSlide = $heroSlides[0] ?? null;
@endphp

@section('title', $homeTitle)
@section('meta_description', $homeDescription)
@section('meta_canonical', route('home'))
@section('meta_type', 'website')
@section('meta_image', $homeImage)
@section('meta_robots', 'index,follow')
@section('mainWidth', 'w-full px-4 sm:mx-auto sm:w-[92%] sm:px-0 lg:w-[90%] 2xl:max-w-[1440px]')

@if($firstHeroSlide)
    @push('head')
        <link rel="preload" as="image" href="{{ $firstHeroSlide['image'] }}" fetchpriority="high">
    @endpush
@endif

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($homeSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

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

            <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl transition-all dark:bg-slate-800">
                <button @click="closePopup()" class="absolute right-3 top-3 z-10 flex items-center gap-1 rounded-full bg-white/80 p-2 text-slate-500 shadow-sm transition hover:text-red-500 dark:bg-slate-700/80">
                    <span class="px-1 text-sm font-bold">إغلاق</span>
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <template x-if="currentPopup && currentPopup.image_path">
                    <img :src="'/storage/' + currentPopup.image_path" width="800" height="480" loading="lazy" decoding="async" class="h-auto max-h-60 w-full object-cover" :alt="currentPopup.localized_title">
                </template>

                <div class="p-6 text-center">
                    <h3 x-text="currentPopup && currentPopup.localized_title" class="mb-2 text-xl font-bold text-slate-800 dark:text-white"></h3>
                    <p x-text="currentPopup && currentPopup.localized_content" class="whitespace-pre-wrap text-slate-600 dark:text-slate-300"></p>

                    <template x-if="currentPopup && currentPopup.localized_button_text && currentPopup.button_url">
                        <a :href="currentPopup.button_url"
                           target="_blank"
                           rel="noopener noreferrer"
                           :style="'background-color:' + (currentPopup.button_color || '#10b981') + ';color:' + (currentPopup.button_text_color || '#ffffff')"
                           class="mt-4 inline-block rounded-full px-6 py-2.5 text-sm font-bold shadow transition hover:opacity-90"
                           x-text="currentPopup.localized_button_text">
                        </a>
                    </template>
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
                            if (sessionStorage.getItem('popup_closed_' + this.popups[0].id)) return;
                            this.currentPopup = this.popups[this.currentIndex];
                        }
                    },

                    closePopup() {
                        sessionStorage.setItem('popup_closed_' + this.currentPopup.id, 'true');
                        this.currentPopup = null;
                        this.currentIndex++;

                        if (this.currentIndex < this.popups.length) {
                            setTimeout(() => {
                                this.currentPopup = this.popups[this.currentIndex];
                            }, 500);
                        }
                    },

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

    @auth
        @inject('loyaltyService', 'App\Services\LoyaltyService')
        @php
            $homeLoyalty = $loyaltyService->summary(auth()->user());
            $homeLoyaltyLabel = $homeLoyalty['level_label'];
            $wallet = auth()->user()->wallet;
            $ordersCount = auth()->user()->orders()->count();
        @endphp
    @endauth

    <div class="home-scene space-y-5 sm:space-y-8">
        <div class="space-y-0">
        <section class="home-hero">

            <div class="home-hero__visual">
                <div class="home-hero-card"
                     x-data="homeHeroSlider({{ count($heroSlides) }})"
                     x-init="start()"
                     @mouseenter="stop()"
                     @mouseleave="start()">
                    <div class="home-hero-card__media rounded-none">
                        @if(!empty($heroSlides))
                            <div class="home-hero-card__viewport">
                                @foreach($heroSlides as $index => $slide)
                                    <div class="home-hero-card__slide"
                                         :class="activeIndex === {{ $index }} ? 'opacity-100 z-[1]' : 'pointer-events-none opacity-0 z-0'">
                                        <img src="{{ $slide['image'] }}"
                                             alt="{{ $slide['title'] }}"
                                             width="1600"
                                             height="700"
                                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                             decoding="async"
                                             @if($index === 0) fetchpriority="high" @endif
                                             class="home-hero-card__image">
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="home-hero-card__fallback">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد صور مفعلة للواجهة حالياً.' : 'No active hero images available yet.' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if(filled((app()->getLocale() === 'en' && !empty($sharedTickerTextEn)) ? $sharedTickerTextEn : $sharedTickerText))
            <section class="home-ticker">
                <div class="home-ticker__label">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تنبيه' : 'Notice' }}</span>
                </div>
                <div class="home-ticker__viewport min-w-0 flex-1 overflow-hidden">
                    <div class="store-ticker-track" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                        {{ (app()->getLocale() === 'en' && !empty($sharedTickerTextEn)) ? $sharedTickerTextEn : $sharedTickerText }}
                    </div>
                </div>
            </section>
        @endif
        </div>

        @auth
            <section class="grid gap-4 lg:grid-cols-[1.45fr_.95fr]">
                <div class="home-account-card">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-3">
                            <span class="home-section-badge">
                                <i class="fa-solid fa-user-shield"></i>
                                {{ app()->getLocale() === 'ar' ? 'تفاصيل حسابك' : 'Your Account Snapshot' }}
                            </span>
                            <div>
                                <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ auth()->user()->name }}</h2>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ app()->getLocale() === 'ar' ? 'عرض سريع لمعلومات الحساب والرصيد والطلبات.' : 'Quick snapshot of your account, balance, and orders.' }}</p>
                            </div>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">
                            <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                            <span>{{ $homeLoyaltyLabel }}</span>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div class="home-balance-tile">
                            <span class="home-balance-tile__label">{{ __('messages.available_balance') }}</span>
                            <strong class="home-balance-tile__value" dir="ltr">$ {{ number_format(auth()->user()->available_balance, 2) }}</strong>
                        </div>
                        <div class="home-balance-tile">
                            <span class="home-balance-tile__label">{{ __('messages.held_balance') }}</span>
                            <strong class="home-balance-tile__value" dir="ltr">$ {{ number_format($wallet?->held_balance ?? 0, 2) }}</strong>
                        </div>
                        <div class="home-balance-tile col-span-2 sm:col-span-1">
                            <span class="home-balance-tile__label">{{ app()->getLocale() === 'ar' ? 'إجمالي الطلبات' : 'Orders Count' }}</span>
                            <strong class="home-balance-tile__value" dir="ltr">{{ $ordersCount }}</strong>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
                        <a href="{{ route('account') }}" class="home-btn home-btn--secondary w-full sm:w-auto">
                            <i class="fa-solid fa-id-card"></i>
                            <span>{{ __('messages.my_account') }}</span>
                        </a>
                        <a href="{{ route('deposit.index') }}" class="home-btn home-btn--ghost w-full sm:w-auto">
                            <i class="fa-solid fa-plus"></i>
                            <span>{{ __('messages.top_up_balance') }}</span>
                        </a>
                    </div>
                </div>

                @if(!auth()->user()->email_verified_at)
                    <div class="home-alert-card home-alert-card--danger">
                        <div class="flex items-start gap-3">
                            <span class="home-alert-card__icon">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-black text-red-700 dark:text-red-300">{{ __('messages.account_activation_required') }}</p>
                                <p class="mt-2 text-sm leading-6 text-red-700/80 dark:text-red-200/90">{{ __('messages.otp_sent_email_instruction') }}</p>
                            </div>
                        </div>

                        <button @click="$dispatch('open-otp-popup')" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-red-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-red-700">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>{{ __('messages.verify') }}</span>
                        </button>
                    </div>
                @endif
            </section>
        @endauth

        <section id="home-categories" class="space-y-5">
            <div class="home-section-heading">
                <div>
                    <h2 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'ابدأ من القسم المناسب لك' : 'Start from the right category' }}</h2>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @forelse ($categories as $category)
                    <x-store.category-card :title="$category->localized_name" :href="route('categories.show', $category->slug)" :image="$category->image_path ? asset('storage/' . $category->image_path) : null" />
                @empty
                    <x-empty-state :message="__('messages.no_categories')" class="col-span-full" />
                @endforelse
            </div>
        </section>

                <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($homeFeatureCards as $featureCard)
                <article class="home-feature-card">
                    <span class="home-feature-card__icon"><i class="{{ $featureCard['icon'] }}"></i></span>
                    <h3 class="home-feature-card__title">{{ $featureCard['title'] }}</h3>
                    <p class="home-feature-card__text">{{ $featureCard['description'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="home-callout">
            <div class="home-callout__content">
                <span class="home-section-badge">
                    <i class="fa-solid fa-store"></i>
                    {{ app()->getLocale() === 'ar' ? 'للبيع بالجملة والطلبات الخاصة' : 'For wholesale and special requests' }}
                </span>
                <h2 class="mt-4 text-2xl font-black text-slate-900 dark:text-white">
                    {{ app()->getLocale() === 'ar' ? 'تمتلك متجر أو تعمل في البيع ؟ تواصل معنا الآن للانضمام الى فريق شحنك شات | S7SH 🔥' : 'Own a shop ? Contact us now to get exclusive wholesale offers' }}
                </h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('agency-requests.create') }}" class="home-btn home-btn--primary">
                    <i class="fa-solid fa-handshake"></i>
                    <span>{{ __('messages.request_agency') }}</span>
                </a>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('homeHeroSlider', (slideCount = 0) => ({
                slideCount,
                activeIndex: 0,
                intervalId: null,
                go(index) {
                    if (!this.slideCount) {
                        return;
                    }

                    const normalizedIndex = ((index % this.slideCount) + this.slideCount) % this.slideCount;
                    this.activeIndex = normalizedIndex;
                },
                next() {
                    this.go(this.activeIndex + 1);
                },
                prev() {
                    this.go(this.activeIndex - 1);
                },
                start() {
                    this.stop();

                    if (this.slideCount < 2) {
                        return;
                    }

                    this.intervalId = window.setInterval(() => {
                        this.next();
                    }, 4500);
                },
                stop() {
                    if (this.intervalId !== null) {
                        window.clearInterval(this.intervalId);
                        this.intervalId = null;
                    }
                },
            }));
        });
    </script>
@endpush
