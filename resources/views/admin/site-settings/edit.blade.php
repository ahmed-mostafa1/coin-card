@extends('layouts.app')

@section('title', 'إعدادات الموقع')
@section('mainWidth', 'w-[85%] max-w-none mx-auto')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-800 dark:text-emerald-400">إعدادات الموقع</h1>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">تحكم في الإعدادات العامة، الشعار، وروابط التواصل الاجتماعي.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-200 dark:bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 transition hover:bg-slate-300 dark:hover:bg-slate-600">
                <i class="fa-solid fa-arrow-right ml-2 rtl:ml-0 rtl:mr-2"></i> {{ __('messages.dashboard') ?? 'لوحة التحكم' }}
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
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 pb-2 border-b border-slate-100 dark:border-slate-700">الإعدادات العامة</h2>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label for="ticker_text" value="نص الشريط المتحرك (عربي)" />
                        <textarea id="ticker_text" name="ticker_text" rows="2" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            required>{{ old('ticker_text', $tickerText) }}</textarea>
                        <x-input-error :messages="$errors->get('ticker_text')" />
                    </div>

                    <div>
                        <x-input-label for="ticker_text_en" value="نص الشريط المتحرك (إنجليزي)" />
                        <textarea id="ticker_text_en" name="ticker_text_en" rows="2" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('ticker_text_en', $tickerTextEn) }}</textarea>
                        <x-input-error :messages="$errors->get('ticker_text_en')" />
                    </div>

                    <div>
                        <x-input-label for="store_description" value="نص الوصف (عربي)" />
                        <textarea id="store_description" name="store_description" rows="4" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            required>{{ old('store_description', $storeDescription) }}</textarea>
                        <x-input-error :messages="$errors->get('store_description')" />
                    </div>

                    <div>
                        <x-input-label for="store_description_en" value="نص الوصف (إنجليزي)" />
                        <textarea id="store_description_en" name="store_description_en" rows="4" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('store_description_en', $storeDescriptionEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('store_description_en')" />
                    </div>


                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>حفظ الإعدادات العامة</x-primary-button>
                </div>
            </div>
        </form>

        <!-- Logo Settings Form -->
        <form action="{{ route('admin.site-settings.update-logo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm" x-data="{ type: '{{ old('logo_type', $logoType) }}' }">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 pb-2 border-b border-slate-100 dark:border-slate-700">إعدادات الشعار</h2>
                
                <div class="space-y-4">
                     <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">نوع الشعار</label>
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_type" value="text" x-model="type" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-600 dark:text-slate-400">نص</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_type" value="image" x-model="type" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-600 dark:text-slate-400">صورة</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="type === 'text'" style="display: none;">
                        <x-input-label for="logo_text" value="نص الشعار" />
                        <x-text-input id="logo_text" name="logo_text" type="text" :value="old('logo_text', $logoText)" class="w-full" />
                        <x-input-error :messages="$errors->get('logo_text')" />
                    </div>

                    <div x-show="type === 'image'" style="display: none;">
                        <x-input-label for="logo_image" value="صورة الشعار" />
                        <div class="mt-2 flex items-center gap-4">
                            @if($logoImage)
                                <div class="h-12 w-12 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-600">
                                    <img src="{{ asset('storage/' . $logoImage) }}" alt="Current Logo" class="h-full w-full object-contain">
                                </div>
                            @endif
                            <input type="file" id="logo_image" name="logo_image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/50 dark:file:text-emerald-400">
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">المقاس المفضل: 200×60 بكسل</p>
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
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-6 pb-2 border-b border-slate-100 dark:border-slate-700">وسائل التواصل الاجتماعي</h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="whatsapp_link" value="رابط واتساب" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-whatsapp text-slate-400"></i>
                            </div>
                            <x-text-input id="whatsapp_link" name="whatsapp_link" type="text" :value="old('whatsapp_link', $whatsappLink)" class="pl-10 text-left" dir="ltr" placeholder="https://wa.me/..." />
                        </div>
                        <x-input-error :messages="$errors->get('whatsapp_link')" />
                    </div>

                    <div>
                        <x-input-label for="instagram_link" value="رابط انستغرام" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-instagram text-slate-400"></i>
                            </div>
                            <x-text-input id="instagram_link" name="instagram_link" type="text" :value="old('instagram_link', $instagramLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('instagram_link')" />
                    </div>

                    <div>
                        <x-input-label for="telegram_link" value="رابط تيليجرام" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-telegram text-slate-400"></i>
                            </div>
                            <x-text-input id="telegram_link" name="telegram_link" type="text" :value="old('telegram_link', $telegramLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('telegram_link')" />
                    </div>

                    <div>
                        <x-input-label for="facebook_link" value="رابط فيسبوك" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-facebook text-slate-400"></i>
                            </div>
                            <x-text-input id="facebook_link" name="facebook_link" type="text" :value="old('facebook_link', $facebookLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('facebook_link')" />
                    </div>
                    
                    <div>
                        <x-input-label for="upscroll_link" value="رابط UpScrolled" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-link text-slate-400"></i>
                            </div>
                            <x-text-input id="upscroll_link" name="upscroll_link" type="text" :value="old('upscroll_link', $upscrollLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('upscroll_link')" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>حفظ روابط التواصل</x-primary-button>
                </div>
            </div>
        </form>


    </div>
@endsection
