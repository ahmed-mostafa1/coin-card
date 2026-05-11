@extends('layouts.app')

@section('title', 'إعدادات الموقع')
@section('mainWidth', 'w-[85%] mx-auto')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-800 dark:text-emerald-100">إعدادات الموقع</h1>
                <p class="mt-1 text-sm text-slate-900 dark:text-slate-100">تحكم في الإعدادات العامة، الشعار، وروابط التواصل الاجتماعي.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-50 dark:bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-slate-50 transition hover:bg-slate-50 dark:hover:bg-slate-900">
                <i class="fa-solid fa-arrow-right ml-2 rtl:ml-0 rtl:mr-2"></i> {{ __('messages.dashboard') ?? 'لوحة التحكم' }}
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- General Settings Form -->
        <form action="{{ route('admin.site-settings.update-general') }}" method="POST">
            @csrf
            <div class="rounded-3xl border border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-50 mb-6 pb-2 border-b border-slate-50 dark:border-slate-700">الإعدادات العامة</h2>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label for="ticker_text" value="نص الشريط المتحرك (عربي)" />
                        <textarea id="ticker_text" name="ticker_text" rows="2" 
                            class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500" 
                            required>{{ old('ticker_text', $tickerText) }}</textarea>
                        <x-input-error :messages="$errors->get('ticker_text')" />
                    </div>

                    <div>
                        <x-input-label for="ticker_text_en" value="نص الشريط المتحرك (إنجليزي)" />
                        <textarea id="ticker_text_en" name="ticker_text_en" rows="2" 
                            class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('ticker_text_en', $tickerTextEn) }}</textarea>
                        <x-input-error :messages="$errors->get('ticker_text_en')" />
                    </div>

                    <div>
                        <x-input-label for="store_description" value="نص الوصف (عربي)" />
                        <textarea id="store_description" name="store_description" rows="4" 
                            class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500" 
                            required>{{ old('store_description', $storeDescription) }}</textarea>
                        <x-input-error :messages="$errors->get('store_description')" />
                    </div>

                    <div>
                        <x-input-label for="store_description_en" value="نص الوصف (إنجليزي)" />
                        <textarea id="store_description_en" name="store_description_en" rows="4" 
                            class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('store_description_en', $storeDescriptionEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('store_description_en')" />
                    </div>
                    <div class="border-t border-slate-50 pt-5 dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-50">نصوص الهيرو في الصفحة الرئيسية</h3>
                        <p class="mt-1 text-sm text-slate-900 dark:text-slate-100">هذه الحقول تتحكم في العنوان والوصف الرئيسي أعلى الصفحة الرئيسية.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="home_hero_title_ar" value="عنوان الهيرو (عربي)" />
                            <x-text-input id="home_hero_title_ar" name="home_hero_title_ar" type="text" :value="old('home_hero_title_ar', $homeHeroTitleAr)" class="w-full" />
                            <x-input-error :messages="$errors->get('home_hero_title_ar')" />
                        </div>
                        <div>
                            <x-input-label for="home_hero_title_en" value="Hero Title (English)" />
                            <x-text-input id="home_hero_title_en" name="home_hero_title_en" type="text" :value="old('home_hero_title_en', $homeHeroTitleEn)" class="w-full" dir="ltr" />
                            <x-input-error :messages="$errors->get('home_hero_title_en')" />
                        </div>
                        <div>
                            <x-input-label for="home_hero_text_ar" value="وصف الهيرو (عربي)" />
                            <textarea id="home_hero_text_ar" name="home_hero_text_ar" rows="3"
                                class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500">{{ old('home_hero_text_ar', $homeHeroTextAr) }}</textarea>
                            <x-input-error :messages="$errors->get('home_hero_text_ar')" />
                        </div>
                        <div>
                            <x-input-label for="home_hero_text_en" value="Hero Description (English)" />
                            <textarea id="home_hero_text_en" name="home_hero_text_en" rows="3" dir="ltr"
                                class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500">{{ old('home_hero_text_en', $homeHeroTextEn) }}</textarea>
                            <x-input-error :messages="$errors->get('home_hero_text_en')" />
                        </div>
                    </div>

                    <div class="border-t border-slate-50 pt-5 dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-50">بطاقات مزايا الصفحة الرئيسية</h3>
                        <p class="mt-1 text-sm text-slate-900 dark:text-slate-100">يمكنك تعديل العنوان والوصف لكل بطاقة كما ستظهر في الصفحة الرئيسية.</p>
                    </div>

                    @foreach($homeFeatureSettings as $feature)
                        <div class="rounded-2xl border border-slate-50 p-4 dark:border-slate-700">
                            <div class="mb-4 flex items-center gap-3">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-100">
                                    <i class="{{ $feature['icon'] }}"></i>
                                </span>
                                <div>
                                    <h4 class="font-semibold text-slate-800 dark:text-slate-50">البطاقة {{ $feature['index'] }}</h4>
                                    <p class="text-xs text-slate-9000 dark:text-slate-50">الأيقونة ثابتة، والنصوص قابلة للتعديل.</p>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <x-input-label for="home_feature_{{ $feature['index'] }}_title_ar" value="العنوان (عربي)" />
                                    <x-text-input id="home_feature_{{ $feature['index'] }}_title_ar" name="home_feature_{{ $feature['index'] }}_title_ar" type="text" :value="old('home_feature_'.$feature['index'].'_title_ar', $feature['title_ar'])" class="w-full" />
                                    <x-input-error :messages="$errors->get('home_feature_'.$feature['index'].'_title_ar')" />
                                </div>
                                <div>
                                    <x-input-label for="home_feature_{{ $feature['index'] }}_title_en" value="Title (English)" />
                                    <x-text-input id="home_feature_{{ $feature['index'] }}_title_en" name="home_feature_{{ $feature['index'] }}_title_en" type="text" :value="old('home_feature_'.$feature['index'].'_title_en', $feature['title_en'])" class="w-full" dir="ltr" />
                                    <x-input-error :messages="$errors->get('home_feature_'.$feature['index'].'_title_en')" />
                                </div>
                                <div>
                                    <x-input-label for="home_feature_{{ $feature['index'] }}_description_ar" value="الوصف (عربي)" />
                                    <textarea id="home_feature_{{ $feature['index'] }}_description_ar" name="home_feature_{{ $feature['index'] }}_description_ar" rows="3"
                                        class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500">{{ old('home_feature_'.$feature['index'].'_description_ar', $feature['description_ar']) }}</textarea>
                                    <x-input-error :messages="$errors->get('home_feature_'.$feature['index'].'_description_ar')" />
                                </div>
                                <div>
                                    <x-input-label for="home_feature_{{ $feature['index'] }}_description_en" value="Description (English)" />
                                    <textarea id="home_feature_{{ $feature['index'] }}_description_en" name="home_feature_{{ $feature['index'] }}_description_en" rows="3" dir="ltr"
                                        class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500">{{ old('home_feature_'.$feature['index'].'_description_en', $feature['description_en']) }}</textarea>
                                    <x-input-error :messages="$errors->get('home_feature_'.$feature['index'].'_description_en')" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>حفظ الإعدادات العامة</x-primary-button>
                </div>
            </div>
        </form>

        <!-- Logo Settings Form -->
        <form action="{{ route('admin.site-settings.update-logo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="rounded-3xl border border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm" x-data="{ type: '{{ old('logo_type', $logoType) }}' }">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-50 mb-6 pb-2 border-b border-slate-50 dark:border-slate-700">إعدادات الشعار</h2>
                
                <div class="space-y-4">
                     <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-50">نوع الشعار</label>
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_type" value="text" x-model="type" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-900 dark:text-slate-100">نص</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_type" value="image" x-model="type" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-900 dark:text-slate-100">صورة</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="logo_text" value="نص الشعار" />
                        <x-text-input id="logo_text" name="logo_text" type="text" :value="old('logo_text', $logoText)" class="w-full" />
                        <p class="mt-1 text-xs text-slate-900 dark:text-slate-100">يظهر هذا النص بجانب الشعار في الهيدر سواء كان الشعار نصًا أو صورة.</p>
                        <x-input-error :messages="$errors->get('logo_text')" />
                    </div>

                    <div x-show="type === 'image'" style="display: none;">
                        <x-input-label for="logo_image" value="صورة الشعار" />
                        <div class="mt-2 flex items-center gap-4">
                            @if($logoImage)
                                <div class="h-12 w-12 rounded bg-slate-50 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-50 dark:border-slate-900">
                                    <img src="{{ asset('storage/' . $logoImage) }}" alt="Current Logo" class="h-full w-full object-contain">
                                </div>
                            @endif
                            <input type="file" id="logo_image" name="logo_image" accept="image/*" class="block w-full text-sm text-slate-900 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-900 hover:file:bg-emerald-100 dark:file:bg-emerald-900/50 dark:file:text-emerald-100">
                            <p class="mt-1 text-xs text-slate-900 dark:text-slate-100">المقاس المفضل: 200×60 بكسل</p>
                        </div>
                        <x-input-error :messages="$errors->get('logo_image')" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>حفظ الشعار</x-primary-button>
                </div>
            </div>
        </form>

        <!-- Social Settings Form -->
        <form action="{{ route('admin.site-settings.update-social') }}" method="POST">
            @csrf
            <div class="rounded-3xl border border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-50 mb-6 pb-2 border-b border-slate-50 dark:border-slate-700">وسائل التواصل الاجتماعي</h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ([
                        ['key' => 'facebook', 'label' => 'فيسبوك', 'icon' => 'fa-facebook', 'link' => $facebookLink, 'enabled' => $facebookEnabled],
                        ['key' => 'telegram', 'label' => 'تيليغرام', 'icon' => 'fa-telegram', 'link' => $telegramLink, 'enabled' => $telegramEnabled],
                        ['key' => 'youtube', 'label' => 'يوتيوب', 'icon' => 'fa-youtube', 'link' => $youtubeLink, 'enabled' => $youtubeEnabled],
                        ['key' => 'whatsapp', 'label' => 'واتساب', 'icon' => 'fa-whatsapp', 'link' => $whatsappLink, 'enabled' => $whatsappEnabled],
                        ['key' => 'tiktok', 'label' => 'تيك توك', 'icon' => 'fa-tiktok', 'link' => $tiktokLink, 'enabled' => $tiktokEnabled],
                    ] as $social)
                        <div class="rounded-2xl border border-slate-50 p-4 dark:border-slate-700">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 dark:bg-slate-700 dark:text-slate-50">
                                        <i class="fa-brands {{ $social['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <h3 class="font-semibold text-slate-800 dark:text-slate-50">{{ $social['label'] }}</h3>
                                        <p class="text-xs text-slate-9000 dark:text-slate-50">رابط + تفعيل للظهور في الفوتر.</p>
                                    </div>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-50">
                                    <input type="checkbox"
                                           name="{{ $social['key'] }}_enabled"
                                           value="1"
                                           class="rounded border-slate-50 text-emerald-600 focus:ring-emerald-500 dark:border-slate-900 dark:bg-slate-700"
                                           @checked(old($social['key'].'_enabled', $social['enabled']) == '1')>
                                    <span>مفعّل</span>
                                </label>
                            </div>

                            <div class="relative mt-4 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fa-brands {{ $social['icon'] }} text-slate-100"></i>
                                </div>
                                <x-text-input id="{{ $social['key'] }}_link"
                                              name="{{ $social['key'] }}_link"
                                              type="text"
                                              :value="old($social['key'].'_link', $social['link'])"
                                              class="pl-10 text-left"
                                              dir="ltr"
                                              placeholder="https://" />
                            </div>
                            <x-input-error :messages="$errors->get($social['key'].'_link')" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>حفظ روابط التواصل</x-primary-button>
                </div>
            </div>
        </form>


        {{-- ── SEO & Ads ────────────────────────────────────────────────── --}}
        <form action="{{ route('admin.site-settings.update-seo') }}" method="POST">
            @csrf
            <div class="rounded-3xl border border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-50 mb-1 pb-2 border-b border-slate-50 dark:border-slate-700">
                    <i class="fa-solid fa-magnifying-glass-chart ml-2 text-emerald-600"></i> SEO والإعلانات
                </h2>
                <p class="text-xs text-slate-9000 dark:text-slate-50 mb-6">تُحفظ في قاعدة البيانات – لن تُفقد عند تحديث الكود.</p>

                <div class="space-y-6">

                    {{-- ── SERP Live Preview ───────────────────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-50 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-4">
                        <p class="mb-3 flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wide">
                            <i class="fa-brands fa-google text-blue-500"></i>
                            معاينة مباشرة – هكذا تظهر في نتائج البحث
                        </p>
                        {{-- Fake Google search bar --}}
                        <div class="mb-3 flex items-center gap-2 rounded-full border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-2 shadow-sm text-sm text-slate-100 max-w-md">
                            <i class="fa-solid fa-magnifying-glass text-slate-100 text-xs"></i>
                            <span class="truncate">{{ config('app.url') }}</span>
                        </div>
                        {{-- SERP Result card --}}
                        <div class="rounded-xl border border-slate-50 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 shadow-sm max-w-2xl">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-5 h-5 rounded-full bg-emerald-600 flex items-center justify-center text-white text-[9px] font-bold">S</div>
                                <div class="text-xs">
                                    <div class="text-slate-800 dark:text-slate-50 font-medium" id="serp_site_name">{{ $seoTitle ?: $logoText }}</div>
                                    <div class="text-slate-900 dark:text-slate-100 text-[11px]" dir="ltr">{{ rtrim(config('app.url'), '/') }} › ...</div>
                                </div>
                            </div>
                            <div id="serp_title_preview"
                                class="text-[17px] font-medium text-blue-700 dark:text-blue-400 leading-snug mb-1 truncate">
                                {{ $seoTitle ?: $logoText }}
                            </div>
                            <div id="serp_desc_preview"
                                class="text-sm text-slate-900 dark:text-slate-100 leading-relaxed line-clamp-2">
                                {{ $metaDescription ?: $storeDescription }}
                            </div>
                        </div>
                    </div>

                    {{-- ── SEO Title ─────────────────────────────────────────────── --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label for="seo_title" value="عنوان الموقع في البحث (Title Tag)" />
                            <span id="seo_title_counter"
                                class="text-xs font-mono tabular-nums transition-colors"
                                :class="...">
                                <span id="seo_title_count">{{ mb_strlen($seoTitle) }}</span>/60
                            </span>
                        </div>
                        <p class="mb-2 text-xs text-slate-9000 dark:text-slate-50">
                            يظهر في تبويب المتصفح وعنوان نتيجة البحث. المثالي: أقل من 60 حرفاً.
                            إذا تُرك فارغاً سيُستخدم اسم الموقع تلقائياً.
                        </p>
                        <x-text-input id="seo_title" name="seo_title" type="text"
                            :value="old('seo_title', $seoTitle)" class="w-full" dir="auto"
                            maxlength="70" placeholder="{{ $logoText }}" />
                        <div id="seo_title_bar" class="mt-1 h-1 rounded-full bg-slate-50 dark:bg-slate-700 overflow-hidden">
                            <div id="seo_title_fill" class="h-full rounded-full transition-all duration-200" style="width:0%"></div>
                        </div>
                        <x-input-error :messages="$errors->get('seo_title')" />
                    </div>

                    {{-- ── Meta Description ──────────────────────────────────────── --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label for="meta_description" value="وصف الموقع في البحث (Meta Description)" />
                            <span id="meta_desc_counter" class="text-xs font-mono tabular-nums transition-colors">
                                <span id="meta_desc_count">{{ mb_strlen($metaDescription) }}</span>/160
                            </span>
                        </div>
                        <p class="mb-2 text-xs text-slate-9000 dark:text-slate-50">
                            الجملة الصغيرة التي تظهر أسفل العنوان في جوجل. الأفضل: 120–160 حرفاً.
                            إذا تُرك فارغاً سيُستخدم وصف المتجر تلقائياً.
                        </p>
                        <textarea id="meta_description" name="meta_description" rows="3" maxlength="500"
                            class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500"
                            dir="auto">{{ old('meta_description', $metaDescription) }}</textarea>
                        <div id="meta_desc_bar" class="mt-1 h-1 rounded-full bg-slate-50 dark:bg-slate-700 overflow-hidden">
                            <div id="meta_desc_fill" class="h-full rounded-full transition-all duration-200" style="width:0%"></div>
                        </div>
                        <x-input-error :messages="$errors->get('meta_description')" />
                    </div>

                    {{-- ── Meta Keywords ─────────────────────────────────────────── --}}
                    <div>
                        <x-input-label for="meta_keywords" value="الكلمات المفتاحية (Keywords)" />
                        <p class="mb-2 text-xs text-slate-9000 dark:text-slate-50">افصل بين الكلمات بفاصلة – مثال: بطاقات العاب، شحن رصيد، خدمات رقمية</p>
                        <x-text-input id="meta_keywords" name="meta_keywords" type="text"
                            :value="old('meta_keywords', $metaKeywords)" class="w-full" dir="auto"
                            placeholder="بطاقات العاب، شحن رصيد، ..." />
                        <x-input-error :messages="$errors->get('meta_keywords')" />
                    </div>

                    {{-- ── Tracking & Pixels ─────────────────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-50 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4">
                        <p class="mb-4 text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wide">
                            <i class="fa-solid fa-chart-line ml-1 text-emerald-500"></i> التتبع والتحليلات
                        </p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="fb_pixel_id" value="Facebook / Meta Pixel ID" />
                                <p class="mb-1 text-xs text-slate-9000 dark:text-slate-50">أرقام فقط – مثال: 1234567890123456</p>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fa-brands fa-meta text-blue-600 text-sm"></i>
                                    </span>
                                    <x-text-input id="fb_pixel_id" name="fb_pixel_id" type="text"
                                        :value="old('fb_pixel_id', $fbPixelId)" class="w-full pr-9" dir="ltr"
                                        placeholder="1234567890123456" />
                                </div>
                                <x-input-error :messages="$errors->get('fb_pixel_id')" />
                            </div>
                            <div>
                                <x-input-label for="ga_id" value="Google Analytics ID" />
                                <p class="mb-1 text-xs text-slate-9000 dark:text-slate-50">مثال: G-XXXXXXXXXX</p>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fa-brands fa-google text-orange-500 text-sm"></i>
                                    </span>
                                    <x-text-input id="ga_id" name="ga_id" type="text"
                                        :value="old('ga_id', $gaId)" class="w-full pr-9" dir="ltr"
                                        placeholder="G-XXXXXXXXXX" />
                                </div>
                                <x-input-error :messages="$errors->get('ga_id')" />
                            </div>
                        </div>
                    </div>

                    {{-- ── Advanced Scripts ──────────────────────────────────────── --}}
                    <details class="rounded-2xl border border-slate-50 dark:border-slate-700 overflow-hidden">
                        <summary class="cursor-pointer select-none bg-slate-50 dark:bg-slate-900/40 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-50 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <i class="fa-solid fa-code ml-2 text-slate-100 text-xs"></i>
                            سكريبتات متقدمة (للمطورين فقط)
                        </summary>
                        <div class="p-4 space-y-4 bg-white dark:bg-slate-800">
                            <div>
                                <x-input-label for="head_scripts" value="كود مخصص داخل <head>" />
                                <p class="mb-1 text-xs text-slate-9000 dark:text-slate-50">مناسب لـ TikTok Pixel أو Google Tag Manager.</p>
                                <textarea id="head_scripts" name="head_scripts" rows="5"
                                    class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-slate-50 dark:bg-slate-900 px-4 py-3 text-sm font-mono text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500"
                                    dir="ltr" placeholder="<!-- أدخل الكود هنا -->">{{ old('head_scripts', $headScripts) }}</textarea>
                                <x-input-error :messages="$errors->get('head_scripts')" />
                            </div>
                            <div>
                                <x-input-label for="body_scripts" value="كود مخصص قبل </body>" />
                                <p class="mb-1 text-xs text-slate-9000 dark:text-slate-50">مناسب لأدوات الدردشة المباشرة.</p>
                                <textarea id="body_scripts" name="body_scripts" rows="5"
                                    class="w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-slate-50 dark:bg-slate-900 px-4 py-3 text-sm font-mono text-slate-700 dark:text-slate-50 focus:border-emerald-500 focus:ring-emerald-500"
                                    dir="ltr" placeholder="<!-- أدخل الكود هنا -->">{{ old('body_scripts', $bodyScripts) }}</textarea>
                                <x-input-error :messages="$errors->get('body_scripts')" />
                            </div>
                        </div>
                    </details>

                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>
                        <i class="fa-solid fa-floppy-disk ml-2"></i> حفظ إعدادات SEO والإعلانات
                    </x-primary-button>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Helpers ────────────────────────────────────────────────────────────
    const TITLE_MAX = 60, DESC_MAX = 160;

    function colorClass(len, soft, hard) {
        if (len === 0) return 'text-slate-100 dark:text-slate-900';
        if (len <= soft) return 'text-emerald-600 dark:text-emerald-100';
        if (len <= hard) return 'text-amber-500';
        return 'text-rose-500';
    }

    function fillColor(len, soft, hard) {
        if (len === 0) return '#cbd5e1';
        if (len <= soft) return '#10b981';
        if (len <= hard) return '#f59e0b';
        return '#ef4444';
    }

    function updateBar(fillEl, len, max, soft, hard) {
        const pct = Math.min(len / max * 100, 100);
        fillEl.style.width = pct + '%';
        fillEl.style.backgroundColor = fillColor(len, soft, hard);
    }

    function updateCounter(counterEl, countSpan, len, soft, hard) {
        countSpan.textContent = len;
        counterEl.className = 'text-xs font-mono tabular-nums transition-colors ' + colorClass(len, soft, hard);
    }

    // ── SEO Title ──────────────────────────────────────────────────────────
    const titleInput   = document.getElementById('seo_title');
    const titleCounter = document.getElementById('seo_title_counter');
    const titleCount   = document.getElementById('seo_title_count');
    const titleFill    = document.getElementById('seo_title_fill');
    const serpTitle    = document.getElementById('serp_title_preview');
    const serpSiteName = document.getElementById('serp_site_name');
    const fallbackTitle = {{ json_encode($logoText) }};

    function syncTitle() {
        const val = titleInput.value.trim();
        const len = [...val].length;          // Unicode-safe
        updateCounter(titleCounter, titleCount, len, 45, TITLE_MAX);
        updateBar(titleFill, len, TITLE_MAX, 45, TITLE_MAX);
        const display = val || fallbackTitle;
        serpTitle.textContent    = display;
        serpSiteName.textContent = display;
    }
    titleInput.addEventListener('input', syncTitle);
    syncTitle();

    // ── Meta Description ───────────────────────────────────────────────────
    const descInput   = document.getElementById('meta_description');
    const descCounter = document.getElementById('meta_desc_counter');
    const descCount   = document.getElementById('meta_desc_count');
    const descFill    = document.getElementById('meta_desc_fill');
    const serpDesc    = document.getElementById('serp_desc_preview');
    const fallbackDesc = {{ json_encode(Str::limit(strip_tags($storeDescription), 160, '')) }};

    function syncDesc() {
        const val = descInput.value.trim();
        const len = [...val].length;
        updateCounter(descCounter, descCount, len, 120, DESC_MAX);
        updateBar(descFill, len, DESC_MAX, 120, DESC_MAX);
        serpDesc.textContent = val || fallbackDesc;
    }
    descInput.addEventListener('input', syncDesc);
    syncDesc();
})();
</script>
@endpush
