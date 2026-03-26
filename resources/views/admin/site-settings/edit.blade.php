@extends('layouts.app')

@section('title', 'Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ù…ÙˆÙ‚Ø¹')
@section('mainWidth', 'w-[85%] max-w-none mx-auto')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-800 dark:text-emerald-400">Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ù…ÙˆÙ‚Ø¹</h1>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">ØªØ­ÙƒÙ… ÙÙŠ Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ø©ØŒ Ø§Ù„Ø´Ø¹Ø§Ø±ØŒ ÙˆØ±ÙˆØ§Ø¨Ø· Ø§Ù„ØªÙˆØ§ØµÙ„ Ø§Ù„Ø§Ø¬ØªÙ…Ø§Ø¹ÙŠ.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-200 dark:bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 transition hover:bg-slate-300 dark:hover:bg-slate-600">
                <i class="fa-solid fa-arrow-right ml-2 rtl:ml-0 rtl:mr-2"></i> {{ __('messages.dashboard') ?? 'Ù„ÙˆØ­Ø© Ø§Ù„ØªØ­ÙƒÙ…' }}
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
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
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 pb-2 border-b border-slate-100 dark:border-slate-700">Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ø©</h2>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label for="ticker_text" value="Ù†Øµ Ø§Ù„Ø´Ø±ÙŠØ· Ø§Ù„Ù…ØªØ­Ø±Ùƒ (Ø¹Ø±Ø¨ÙŠ)" />
                        <textarea id="ticker_text" name="ticker_text" rows="2" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            required>{{ old('ticker_text', $tickerText) }}</textarea>
                        <x-input-error :messages="$errors->get('ticker_text')" />
                    </div>

                    <div>
                        <x-input-label for="ticker_text_en" value="Ù†Øµ Ø§Ù„Ø´Ø±ÙŠØ· Ø§Ù„Ù…ØªØ­Ø±Ùƒ (Ø¥Ù†Ø¬Ù„ÙŠØ²ÙŠ)" />
                        <textarea id="ticker_text_en" name="ticker_text_en" rows="2" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('ticker_text_en', $tickerTextEn) }}</textarea>
                        <x-input-error :messages="$errors->get('ticker_text_en')" />
                    </div>

                    <div>
                        <x-input-label for="store_description" value="Ù†Øµ Ø§Ù„ÙˆØµÙ (Ø¹Ø±Ø¨ÙŠ)" />
                        <textarea id="store_description" name="store_description" rows="4" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            required>{{ old('store_description', $storeDescription) }}</textarea>
                        <x-input-error :messages="$errors->get('store_description')" />
                    </div>

                    <div>
                        <x-input-label for="store_description_en" value="Ù†Øµ Ø§Ù„ÙˆØµÙ (Ø¥Ù†Ø¬Ù„ÙŠØ²ÙŠ)" />
                        <textarea id="store_description_en" name="store_description_en" rows="4" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('store_description_en', $storeDescriptionEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('store_description_en')" />
                    </div>


                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>Ø­ÙØ¸ Ø§Ù„Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ø©</x-primary-button>
                </div>
            </div>
        </form>

        <!-- Logo Settings Form -->
        <form action="{{ route('admin.site-settings.update-logo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm" x-data="{ type: '{{ old('logo_type', $logoType) }}' }">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 pb-2 border-b border-slate-100 dark:border-slate-700">Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª Ø§Ù„Ø´Ø¹Ø§Ø±</h2>
                
                <div class="space-y-4">
                     <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Ù†ÙˆØ¹ Ø§Ù„Ø´Ø¹Ø§Ø±</label>
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_type" value="text" x-model="type" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-600 dark:text-slate-400">Ù†Øµ</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_type" value="image" x-model="type" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-600 dark:text-slate-400">ØµÙˆØ±Ø©</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="type === 'text'" style="display: none;">
                        <x-input-label for="logo_text" value="Ù†Øµ Ø§Ù„Ø´Ø¹Ø§Ø±" />
                        <x-text-input id="logo_text" name="logo_text" type="text" :value="old('logo_text', $logoText)" class="w-full" />
                        <x-input-error :messages="$errors->get('logo_text')" />
                    </div>

                    <div x-show="type === 'image'" style="display: none;">
                        <x-input-label for="logo_image" value="ØµÙˆØ±Ø© Ø§Ù„Ø´Ø¹Ø§Ø±" />
                        <div class="mt-2 flex items-center gap-4">
                            @if($logoImage)
                                <div class="h-12 w-12 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-600">
                                    <img src="{{ asset('storage/' . $logoImage) }}" alt="Current Logo" class="h-full w-full object-contain">
                                </div>
                            @endif
                            <input type="file" id="logo_image" name="logo_image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/50 dark:file:text-emerald-400">
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ø§Ù„Ù…Ù‚Ø§Ø³ Ø§Ù„Ù…ÙØ¶Ù„: 200Ã—60 Ø¨ÙƒØ³Ù„</p>
                        </div>
                        <x-input-error :messages="$errors->get('logo_image')" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>Ø­ÙØ¸ Ø§Ù„Ø´Ø¹Ø§Ø±</x-primary-button>
                </div>
            </div>
        </form>

        <!-- Social Settings Form -->
        <form action="{{ route('admin.site-settings.update-social') }}" method="POST">
            @csrf
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 pb-2 border-b border-slate-100 dark:border-slate-700">ÙˆØ³Ø§Ø¦Ù„ Ø§Ù„ØªÙˆØ§ØµÙ„ Ø§Ù„Ø§Ø¬ØªÙ…Ø§Ø¹ÙŠ</h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="whatsapp_link" value="Ø±Ø§Ø¨Ø· ÙˆØ§ØªØ³Ø§Ø¨" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-whatsapp text-slate-400"></i>
                            </div>
                            <x-text-input id="whatsapp_link" name="whatsapp_link" type="text" :value="old('whatsapp_link', $whatsappLink)" class="pl-10 text-left" dir="ltr" placeholder="https://wa.me/..." />
                        </div>
                        <x-input-error :messages="$errors->get('whatsapp_link')" />
                    </div>

                    <div>
                        <x-input-label for="instagram_link" value="Ø±Ø§Ø¨Ø· Ø§Ù†Ø³ØªØºØ±Ø§Ù…" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-instagram text-slate-400"></i>
                            </div>
                            <x-text-input id="instagram_link" name="instagram_link" type="text" :value="old('instagram_link', $instagramLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('instagram_link')" />
                    </div>

                    <div>
                        <x-input-label for="telegram_link" value="Ø±Ø§Ø¨Ø· ØªÙŠÙ„ÙŠØ¬Ø±Ø§Ù…" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-telegram text-slate-400"></i>
                            </div>
                            <x-text-input id="telegram_link" name="telegram_link" type="text" :value="old('telegram_link', $telegramLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('telegram_link')" />
                    </div>

                    <div>
                        <x-input-label for="facebook_link" value="Ø±Ø§Ø¨Ø· ÙÙŠØ³Ø¨ÙˆÙƒ" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-facebook text-slate-400"></i>
                            </div>
                            <x-text-input id="facebook_link" name="facebook_link" type="text" :value="old('facebook_link', $facebookLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('facebook_link')" />
                    </div>
                    
                    <div>
                        <x-input-label for="youtube_link" value="Ø±Ø§Ø¨Ø· ÙŠÙˆØªÙŠÙˆØ¨" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-youtube text-slate-400"></i>
                            </div>
                            <x-text-input id="youtube_link" name="youtube_link" type="text" :value="old('youtube_link', $youtubeLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('youtube_link')" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>Ø­ÙØ¸ Ø±ÙˆØ§Ø¨Ø· Ø§Ù„ØªÙˆØ§ØµÙ„</x-primary-button>
                </div>
            </div>
        </form>


        {{-- â”€â”€ SEO & Ads â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <form action="{{ route('admin.site-settings.update-seo') }}" method="POST">
            @csrf
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-1 pb-2 border-b border-slate-100 dark:border-slate-700">
                    <i class="fa-solid fa-magnifying-glass-chart ml-2 text-emerald-600"></i> SEO ÙˆØ§Ù„Ø¥Ø¹Ù„Ø§Ù†Ø§Øª
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">ØªÙØ­ÙØ¸ ÙÙŠ Ù‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª â€” Ù„Ù† ØªÙÙÙ‚Ø¯ Ø¹Ù†Ø¯ ØªØ­Ø¯ÙŠØ« Ø§Ù„ÙƒÙˆØ¯.</p>

                <div class="space-y-6">

                    {{-- â”€â”€ SERP Live Preview â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-4">
                        <p class="mb-3 flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            <i class="fa-brands fa-google text-blue-500"></i>
                            Ù…Ø¹Ø§ÙŠÙ†Ø© Ù…Ø¨Ø§Ø´Ø±Ø© â€” Ù‡ÙƒØ°Ø§ ØªØ¸Ù‡Ø± ÙÙŠ Ù†ØªØ§Ø¦Ø¬ Ø§Ù„Ø¨Ø­Ø«
                        </p>
                        {{-- Fake Google search bar --}}
                        <div class="mb-3 flex items-center gap-2 rounded-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 shadow-sm text-sm text-slate-400 max-w-md">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                            <span class="truncate">{{ config('app.url') }}</span>
                        </div>
                        {{-- SERP Result card --}}
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 shadow-sm max-w-2xl">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-5 h-5 rounded-full bg-emerald-600 flex items-center justify-center text-white text-[9px] font-bold">S</div>
                                <div class="text-xs">
                                    <div class="text-slate-800 dark:text-slate-200 font-medium" id="serp_site_name">{{ $seoTitle ?: $logoText }}</div>
                                    <div class="text-slate-500 dark:text-slate-400 text-[11px]" dir="ltr">{{ rtrim(config('app.url'), '/') }} â€º ...</div>
                                </div>
                            </div>
                            <div id="serp_title_preview"
                                class="text-[17px] font-medium text-blue-700 dark:text-blue-400 leading-snug mb-1 truncate">
                                {{ $seoTitle ?: $logoText }}
                            </div>
                            <div id="serp_desc_preview"
                                class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2">
                                {{ $metaDescription ?: $storeDescription }}
                            </div>
                        </div>
                    </div>

                    {{-- â”€â”€ SEO Title â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label for="seo_title" value="Ø¹Ù†ÙˆØ§Ù† Ø§Ù„Ù…ÙˆÙ‚Ø¹ ÙÙŠ Ø§Ù„Ø¨Ø­Ø« (Title Tag)" />
                            <span id="seo_title_counter"
                                class="text-xs font-mono tabular-nums transition-colors"
                                :class="...">
                                <span id="seo_title_count">{{ mb_strlen($seoTitle) }}</span>/60
                            </span>
                        </div>
                        <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">
                            ÙŠØ¸Ù‡Ø± ÙÙŠ ØªØ¨ÙˆÙŠØ¨ Ø§Ù„Ù…ØªØµÙØ­ ÙˆØ¹Ù†ÙˆØ§Ù† Ù†ØªÙŠØ¬Ø© Ø§Ù„Ø¨Ø­Ø«. Ø§Ù„Ù…Ø«Ø§Ù„ÙŠ: Ø£Ù‚Ù„ Ù…Ù† 60 Ø­Ø±ÙØ§Ù‹.
                            Ø¥Ø°Ø§ ØªÙØ±Ùƒ ÙØ§Ø±ØºØ§Ù‹ Ø³ÙŠÙØ³ØªØ®Ø¯Ù… Ø§Ø³Ù… Ø§Ù„Ù…ÙˆÙ‚Ø¹ ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹.
                        </p>
                        <x-text-input id="seo_title" name="seo_title" type="text"
                            :value="old('seo_title', $seoTitle)" class="w-full" dir="auto"
                            maxlength="70" placeholder="{{ $logoText }}" />
                        <div id="seo_title_bar" class="mt-1 h-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            <div id="seo_title_fill" class="h-full rounded-full transition-all duration-200" style="width:0%"></div>
                        </div>
                        <x-input-error :messages="$errors->get('seo_title')" />
                    </div>

                    {{-- â”€â”€ Meta Description â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label for="meta_description" value="ÙˆØµÙ Ø§Ù„Ù…ÙˆÙ‚Ø¹ ÙÙŠ Ø§Ù„Ø¨Ø­Ø« (Meta Description)" />
                            <span id="meta_desc_counter" class="text-xs font-mono tabular-nums transition-colors">
                                <span id="meta_desc_count">{{ mb_strlen($metaDescription) }}</span>/160
                            </span>
                        </div>
                        <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">
                            Ø§Ù„Ø¬Ù…Ù„Ø© Ø§Ù„ØµØºÙŠØ±Ø© Ø§Ù„ØªÙŠ ØªØ¸Ù‡Ø± Ø£Ø³ÙÙ„ Ø§Ù„Ø¹Ù†ÙˆØ§Ù† ÙÙŠ Ø¬ÙˆØ¬Ù„. Ø§Ù„Ø£ÙØ¶Ù„: 120â€“160 Ø­Ø±ÙØ§Ù‹.
                            Ø¥Ø°Ø§ ØªÙØ±Ùƒ ÙØ§Ø±ØºØ§Ù‹ Ø³ÙŠÙØ³ØªØ®Ø¯Ù… ÙˆØµÙ Ø§Ù„Ù…ØªØ¬Ø± ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹.
                        </p>
                        <textarea id="meta_description" name="meta_description" rows="3" maxlength="500"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                            dir="auto">{{ old('meta_description', $metaDescription) }}</textarea>
                        <div id="meta_desc_bar" class="mt-1 h-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            <div id="meta_desc_fill" class="h-full rounded-full transition-all duration-200" style="width:0%"></div>
                        </div>
                        <x-input-error :messages="$errors->get('meta_description')" />
                    </div>

                    {{-- â”€â”€ Meta Keywords â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div>
                        <x-input-label for="meta_keywords" value="Ø§Ù„ÙƒÙ„Ù…Ø§Øª Ø§Ù„Ù…ÙØªØ§Ø­ÙŠØ© (Keywords)" />
                        <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">Ø§ÙØµÙ„ Ø¨ÙŠÙ† Ø§Ù„ÙƒÙ„Ù…Ø§Øª Ø¨ÙØ§ØµÙ„Ø© â€” Ù…Ø«Ø§Ù„: Ø¨Ø·Ø§Ù‚Ø§Øª Ø§Ù„Ø¹Ø§Ø¨ØŒ Ø´Ø­Ù† Ø±ØµÙŠØ¯ØŒ Ø®Ø¯Ù…Ø§Øª Ø±Ù‚Ù…ÙŠØ©</p>
                        <x-text-input id="meta_keywords" name="meta_keywords" type="text"
                            :value="old('meta_keywords', $metaKeywords)" class="w-full" dir="auto"
                            placeholder="Ø¨Ø·Ø§Ù‚Ø§Øª Ø§Ù„Ø¹Ø§Ø¨ØŒ Ø´Ø­Ù† Ø±ØµÙŠØ¯ØŒ ..." />
                        <x-input-error :messages="$errors->get('meta_keywords')" />
                    </div>

                    {{-- â”€â”€ Tracking & Pixels â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4">
                        <p class="mb-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            <i class="fa-solid fa-chart-line ml-1 text-emerald-500"></i> Ø§Ù„ØªØªØ¨Ø¹ ÙˆØ§Ù„ØªØ­Ù„ÙŠÙ„Ø§Øª
                        </p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="fb_pixel_id" value="Facebook / Meta Pixel ID" />
                                <p class="mb-1 text-xs text-slate-500 dark:text-slate-400">Ø£Ø±Ù‚Ø§Ù… ÙÙ‚Ø· â€” Ù…Ø«Ø§Ù„: 1234567890123456</p>
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
                                <p class="mb-1 text-xs text-slate-500 dark:text-slate-400">Ù…Ø«Ø§Ù„: G-XXXXXXXXXX</p>
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

                    {{-- â”€â”€ Advanced Scripts â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <details class="rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <summary class="cursor-pointer select-none bg-slate-50 dark:bg-slate-900/40 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition">
                            <i class="fa-solid fa-code ml-2 text-slate-400 text-xs"></i>
                            Ø³ÙƒØ±ÙŠØ¨ØªØ§Øª Ù…ØªÙ‚Ø¯Ù…Ø© (Ù„Ù„Ù…Ø·ÙˆØ±ÙŠÙ† ÙÙ‚Ø·)
                        </summary>
                        <div class="p-4 space-y-4 bg-white dark:bg-slate-800">
                            <div>
                                <x-input-label for="head_scripts" value="ÙƒÙˆØ¯ Ù…Ø®ØµØµ Ø¯Ø§Ø®Ù„ <head>" />
                                <p class="mb-1 text-xs text-slate-500 dark:text-slate-400">Ù…Ù†Ø§Ø³Ø¨ Ù„Ù€ TikTok Pixel Ø£Ùˆ Google Tag Manager.</p>
                                <textarea id="head_scripts" name="head_scripts" rows="5"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 px-4 py-3 text-sm font-mono text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    dir="ltr" placeholder="<!-- Ø£Ø¯Ø®Ù„ Ø§Ù„ÙƒÙˆØ¯ Ù‡Ù†Ø§ -->">{{ old('head_scripts', $headScripts) }}</textarea>
                                <x-input-error :messages="$errors->get('head_scripts')" />
                            </div>
                            <div>
                                <x-input-label for="body_scripts" value="ÙƒÙˆØ¯ Ù…Ø®ØµØµ Ù‚Ø¨Ù„ </body>" />
                                <p class="mb-1 text-xs text-slate-500 dark:text-slate-400">Ù…Ù†Ø§Ø³Ø¨ Ù„Ø£Ø¯ÙˆØ§Øª Ø§Ù„Ø¯Ø±Ø¯Ø´Ø© Ø§Ù„Ù…Ø¨Ø§Ø´Ø±Ø©.</p>
                                <textarea id="body_scripts" name="body_scripts" rows="5"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 px-4 py-3 text-sm font-mono text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    dir="ltr" placeholder="<!-- Ø£Ø¯Ø®Ù„ Ø§Ù„ÙƒÙˆØ¯ Ù‡Ù†Ø§ -->">{{ old('body_scripts', $bodyScripts) }}</textarea>
                                <x-input-error :messages="$errors->get('body_scripts')" />
                            </div>
                        </div>
                    </details>

                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>
                        <i class="fa-solid fa-floppy-disk ml-2"></i> Ø­ÙØ¸ Ø¥Ø¹Ø¯Ø§Ø¯Ø§Øª SEO ÙˆØ§Ù„Ø¥Ø¹Ù„Ø§Ù†Ø§Øª
                    </x-primary-button>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
(function () {
    // â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const TITLE_MAX = 60, DESC_MAX = 160;

    function colorClass(len, soft, hard) {
        if (len === 0) return 'text-slate-400 dark:text-slate-500';
        if (len <= soft) return 'text-emerald-600 dark:text-emerald-400';
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

    // â”€â”€ SEO Title â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

    // â”€â”€ Meta Description â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
