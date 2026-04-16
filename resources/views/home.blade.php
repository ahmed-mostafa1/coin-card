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
    $featuredServicesCount = $featuredServices->count();
    $heroSlides = $sharedBanners
        ->filter(fn ($banner) => filled($banner->image_path))
        ->values()
        ->map(fn ($banner) => [
            'image' => asset('storage/' . $banner->image_path),
            'title' => $banner->localized_title ?: __('messages.home'),
        ])
        ->all();
@endphp

@section('title', $homeTitle)
@section('meta_description', $homeDescription)
@section('meta_canonical', route('home'))
@section('meta_type', 'website')
@section('meta_image', $homeImage)
@section('meta_robots', 'index,follow')
@section('mainWidth', 'w-full px-4 sm:w-[85%] sm:px-0 sm:mx-auto')

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
                    <img :src="'/storage/' + currentPopup.image_path" class="h-auto max-h-60 w-full object-cover" :alt="currentPopup.localized_title">
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
        @inject('vipService', 'App\Services\VipService')
        @php
            $homeVip = $vipService->getVipSummary(auth()->user());
            $homeVipTier = $homeVip['current_tier'];
            $homeVipLabel = $homeVipTier
                ? (app()->getLocale() === 'ar' ? $homeVipTier->title_ar : ($homeVipTier->title_en ?: $homeVipTier->title_ar))
                : __('messages.no_level');
            $wallet = auth()->user()->wallet;
        @endphp
    @endauth

    <div class="home-scene space-y-5 sm:space-y-8">
        <section class="home-hero" style="width: 100vw; margin-inline: calc(50% - 50vw);">

            <div class="home-hero__visual">
                <div class="home-hero-card"
                     x-data="homeHeroSlider(@js($heroSlides))"
                     x-init="start()"
                     @mouseenter="stop()"
                     @mouseleave="start()"
                     :style="`--home-hero-ratio: ${heroRatio}`">
                    <div class="home-hero-card__media min-h-[18rem] rounded-none lg:max-h-[23rem]">
                        @if(!empty($heroSlides))
                            <div class="home-hero-card__chrome">
                                <span class="home-hero-card__badge">
                                    <i class="fa-solid fa-star"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'عروض مميزة' : 'Featured offers' }}</span>
                                </span>

                                <div class="home-hero-card__caption" x-show="slides.length" x-cloak>
                                    <span class="truncate" x-text="slides[activeIndex] && slides[activeIndex].title ? slides[activeIndex].title : ''"></span>
                                    <span class="shrink-0" dir="ltr" x-text="`${activeIndex + 1} / ${slides.length}`"></span>
                                </div>
                            </div>

                            <div class="home-hero-card__viewport">
                                <template x-for="(slide, index) in slides" :key="`${index}-${slide.image}`">
                                    <div class="home-hero-card__slide"
                                         :class="activeIndex === index ? 'opacity-100 z-[1]' : 'pointer-events-none opacity-0 z-0'">
                                        <img :src="slide.image"
                                             :alt="slide.title"
                                             class="home-hero-card__image"
                                             @load="updateRatio($event.target, index)">
                                    </div>
                                </template>

                                @if(count($heroSlides) > 1)
                                    <div class="home-hero-card__controls">
                                        <button type="button" class="home-hero-card__nav" @click="next()" aria-label="Next slide">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </button>

                                        <div class="home-hero-card__dots">
                                            <template x-for="(slide, index) in slides" :key="`indicator-${index}`">
                                                <button type="button"
                                                        class="home-hero-card__dot"
                                                        :class="activeIndex === index ? 'home-hero-card__dot--active' : ''"
                                                        @click="go(index)"
                                                        :aria-label="slide.title || `Slide ${index + 1}`"></button>
                                            </template>
                                        </div>

                                        <button type="button" class="home-hero-card__nav" @click="prev()" aria-label="Previous slide">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </button>
                                    </div>
                                @endif
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
                <div class="min-w-0 flex-1 overflow-hidden">
                    <div class="store-ticker-track">
                        {{ (app()->getLocale() === 'en' && !empty($sharedTickerTextEn)) ? $sharedTickerTextEn : $sharedTickerText }}
                    </div>
                </div>
            </section>
        @endif

        @auth
            <section class="grid gap-4 lg:grid-cols-[1.45fr_.95fr]">
                <div class="home-account-card">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-3">
                            <span class="home-section-badge">
                                <i class="fa-solid fa-user-shield"></i>
                                {{ app()->getLocale() === 'ar' ? 'ملخص حسابك' : 'Your Account Snapshot' }}
                            </span>
                            <div>
                                <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ auth()->user()->name }}</h2>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                    {{ app()->getLocale() === 'ar' ? 'كل ما تحتاجه للبدء موجود هنا: الرصيد، حالة التفعيل، والمستوى الحالي.' : 'Balance, verification status, and your current rank in one place.' }}
                                </p>
                            </div>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">
                            <i class="fa-solid fa-crown text-amber-400"></i>
                            <span>{{ $homeVipLabel }}</span>
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
                            <span class="home-balance-tile__label">{{ app()->getLocale() === 'ar' ? 'حالة البريد' : 'Email Status' }}</span>
                            <strong class="home-balance-tile__value text-base">
                                {{ auth()->user()->email_verified_at ? (app()->getLocale() === 'ar' ? 'مفعل' : 'Verified') : (app()->getLocale() === 'ar' ? 'بحاجة إلى تفعيل' : 'Needs verification') }}
                            </strong>
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
                @else
                    <div class="home-alert-card home-alert-card--success">
                        <div class="flex items-start gap-3">
                            <span class="home-alert-card__icon">
                                <i class="fa-solid fa-circle-check"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-black text-emerald-700 dark:text-emerald-300">{{ app()->getLocale() === 'ar' ? 'الحساب جاهز للشراء' : 'Account is ready' }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                    {{ app()->getLocale() === 'ar' ? 'يمكنك متابعة الأقسام المتاحة وشراء الخدمات مباشرة من رصيدك الحالي.' : 'You can browse categories and purchase directly using your wallet balance.' }}
                                </p>
                            </div>
                        </div>

                        <a href="#home-services" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">
                            <i class="fa-solid fa-arrow-down"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'انتقل إلى الخدمات' : 'Go to services' }}</span>
                        </a>
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
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse ($categories as $category)
                    <x-store.category-card :title="$category->localized_name" :href="route('categories.show', $category->slug)" :image="$category->image_path ? asset('storage/' . $category->image_path) : null" />
                    @if (false)
                        <div class="home-category-card__image">
                            @if($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->localized_name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-100 to-white dark:from-emerald-950/40 dark:to-slate-900">
                                    <i class="fa-solid fa-layer-group text-3xl text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col justify-between p-5">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $category->localized_name }}</h3>
                                    <span class="home-category-card__arrow">
                                        <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-xs"></i>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'تصفح الخدمات المرتبطة بهذا القسم ضمن واجهة مرتبة وسريعة.'
                                        : 'Browse services in this category through a cleaner and faster layout.' }}
                                </p>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <span class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ app()->getLocale() === 'ar' ? 'خدمات متاحة' : 'Available' }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">{{ $category->services_count }}</span>
                            </div>
                        </div>
                    </a>
                    @endif
                @empty
                    <x-empty-state :message="__('messages.no_categories')" class="md:col-span-2 xl:col-span-4" />
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

        
        <section id="home-services" class="space-y-5">
            <div class="home-section-heading">
                <div>
                    <h2 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ __('messages.featured_services') }}</h2>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse($featuredServices as $service)
                    <x-store.product-card :service="$service" layout="feature" />
                @empty
                    <x-empty-state :message="__('messages.no_services')" class="md:col-span-2 xl:col-span-4" />
                @endforelse
            </div>
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
            Alpine.data('homeHeroSlider', (slides = []) => ({
                slides,
                activeIndex: 0,
                heroRatio: '16 / 7',
                intervalId: null,
                syncRatio() {
                    this.$nextTick(() => {
                        const activeImage = this.$root.querySelectorAll('.home-hero-card__image')[this.activeIndex];

                        if (activeImage && activeImage.complete) {
                            this.updateRatio(activeImage, this.activeIndex);
                        }
                    });
                },
                updateRatio(image, index) {
                    if (!image || index !== this.activeIndex || !image.naturalWidth || !image.naturalHeight) {
                        return;
                    }

                    this.heroRatio = `${image.naturalWidth} / ${image.naturalHeight}`;
                },
                go(index) {
                    if (!Array.isArray(this.slides) || !this.slides.length) {
                        return;
                    }

                    const normalizedIndex = ((index % this.slides.length) + this.slides.length) % this.slides.length;
                    this.activeIndex = normalizedIndex;
                    this.syncRatio();
                },
                next() {
                    this.go(this.activeIndex + 1);
                },
                prev() {
                    this.go(this.activeIndex - 1);
                },
                start() {
                    this.stop();
                    this.syncRatio();

                    if (!Array.isArray(this.slides) || this.slides.length < 2) {
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
