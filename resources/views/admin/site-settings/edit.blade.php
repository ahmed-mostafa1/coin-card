@extends('layouts.app')

@section('title', 'إدارة الموقع')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">إدارة الموقع</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">إدارة إعدادات الموقع العامة</p>
            </div>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('messages.return_to_dashboard') }}
            </a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data" class="mt-6 space-y-8">
            @csrf

            <!-- البانرات Section -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200">البانرات</h2>
                    <a href="{{ route('admin.banners.index') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        إدارة البانرات
                    </a>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">يمكنك إدارة صور البانرات من خلال الصفحة المخصصة</p>
                
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <i class="fa-solid fa-info-circle ml-2"></i>
                        <strong>المقاس المفضل:</strong> عرض 1400 بكسل × طول 400 بكسل
                    </p>
                </div>
            </div>

            <!-- النوافذ المنبثقة Section -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200">النوافذ المنبثقة</h2>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">
                            {{ $activePopupsCount }} نشطة
                        </span>
                    </div>
                    <a href="{{ route('admin.popups.index') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                         إدارة النوافذ
                    </a>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">إدارة النوافذ الإعلانية التي تظهر في الصفحة الرئيسية.</p>
            </div>

            <!-- الشريط المتحرك Section -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">الشريط المتحرك</h2>
                
                <div>
                    <x-input-label for="ticker_text" value="نص الشريط المتحرك (عربي)" />
                    <textarea id="ticker_text" name="ticker_text" rows="3" 
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                        required>{{ old('ticker_text', $tickerText) }}</textarea>
                    <x-input-error :messages="$errors->get('ticker_text')" />
                </div>

                <div class="mt-4">
                    <x-input-label for="ticker_text_en" value="نص الشريط المتحرك (إنجليزي)" />
                    <textarea id="ticker_text_en" name="ticker_text_en" rows="3" 
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                        dir="ltr">{{ old('ticker_text_en', $tickerTextEn) }}</textarea>
                    <x-input-error :messages="$errors->get('ticker_text_en')" />
                </div>
            </div>

            <!-- Logo Settings Section -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">إعدادات الشعار</h2>
                
                <div class="space-y-4">
                    <!-- Logo Type Selection -->
                    <div>
                        <x-input-label for="logo_type" value="نوع الشعار" />
                        <div class="mt-2 space-y-2">
                            <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <input type="radio" name="logo_type" value="text" 
                                    {{ old('logo_type', $logoType) === 'text' ? 'checked' : '' }}
                                    class="text-emerald-600 focus:ring-emerald-500"
                                    onchange="toggleLogoInputs(this.value)">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">نص</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <input type="radio" name="logo_type" value="image" 
                                    {{ old('logo_type', $logoType) === 'image' ? 'checked' : '' }}
                                    class="text-emerald-600 focus:ring-emerald-500"
                                    onchange="toggleLogoInputs(this.value)">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">صورة</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('logo_type')" />
                    </div>

                    <!-- Logo Text Input -->
                    <div id="logo_text_input" style="display: {{ old('logo_type', $logoType) === 'text' ? 'block' : 'none' }}">
                        <x-input-label for="logo_text" value="نص الشعار" />
                        <input type="text" id="logo_text" name="logo_text" 
                            value="{{ old('logo_text', $logoText) }}"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Arab 8BP.in">
                        <x-input-error :messages="$errors->get('logo_text')" />
                    </div>

                    <!-- Logo Image Input -->
                    <div id="logo_image_input" style="display: {{ old('logo_type', $logoType) === 'image' ? 'block' : 'none' }}">
                        <x-input-label for="logo_image" value="صورة الشعار" />
                        
                        @if($logoImage && $logoType === 'image')
                            <div class="mt-2 mb-3">
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">الشعار الحالي:</p>
                                <img src="{{ asset('storage/' . $logoImage) }}" alt="Logo" class="h-12 object-contain border border-slate-200 dark:border-slate-600 rounded-lg p-2 bg-white dark:bg-slate-800">
                            </div>
                        @endif
                        
                        <input type="file" id="logo_image" name="logo_image" accept="image/*"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-info-circle ml-1"></i>
                            المقاس المفضل: عرض 200 بكسل × طول 50 بكسل (الحد الأقصى: 2 ميجابايت)
                        </p>
                        <x-input-error :messages="$errors->get('logo_image')" />
                    </div>
                </div>
            </div>



            <!-- Store Description Section -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">وصف المتجر</h2>
                
                <div>
                    <x-input-label for="store_description" value="نص الوصف (عربي)" />
                    <textarea id="store_description" name="store_description" rows="4" 
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                        required>{{ old('store_description', $storeDescription) }}</textarea>
                    <x-input-error :messages="$errors->get('store_description')" />
                </div>

                <div class="mt-4">
                    <x-input-label for="store_description_en" value="نص الوصف (إنجليزي)" />
                    <textarea id="store_description_en" name="store_description_en" rows="4" 
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                        dir="ltr">{{ old('store_description_en', $storeDescriptionEn ?? '') }}</textarea>
                    <x-input-error :messages="$errors->get('store_description_en')" />
                </div>
            </div>

            <!-- صفحات المحتوى Section -->
             <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6" x-data="{ tab: 'about' }">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">صفحات المحتوى</h2>
                
                <div class="flex gap-2 mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                    <button type="button" @click="tab = 'about'" :class="{ 'text-emerald-600 border-b-2 border-emerald-600 font-bold': tab === 'about', 'text-slate-500 hover:text-slate-700': tab !== 'about' }" class="px-4 py-2 text-sm transition">من نحن (About Us)</button>
                    <button type="button" @click="tab = 'privacy'" :class="{ 'text-emerald-600 border-b-2 border-emerald-600 font-bold': tab === 'privacy', 'text-slate-500 hover:text-slate-700': tab !== 'privacy' }" class="px-4 py-2 text-sm transition">سياسة الخصوصية (Privacy)</button>
                </div>

                <!-- About Us Tab -->
                <div x-show="tab === 'about'" class="space-y-4">
                    <div>
                        <x-input-label for="about_ar" value="من نحن (عربي)" />
                        <textarea id="about_ar" name="about_ar" rows="6" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            >{{ old('about_ar', $aboutAr ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('about_ar')" />
                    </div>
                    <div>
                        <x-input-label for="about_en" value="من نحن (إنجليزي)" />
                        <textarea id="about_en" name="about_en" rows="6" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('about_en', $aboutEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('about_en')" />
                    </div>
                </div>

                <!-- Privacy Tab -->
                <div x-show="tab === 'privacy'" class="space-y-4" style="display: none;">
                    <div>
                        <x-input-label for="privacy_ar" value="سياسة الخصوصية (عربي)" />
                        <textarea id="privacy_ar" name="privacy_ar" rows="6" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            >{{ old('privacy_ar', $privacyAr ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('privacy_ar')" />
                    </div>
                    <div>
                        <x-input-label for="privacy_en" value="سياسة الخصوصية (إنجليزي)" />
                        <textarea id="privacy_en" name="privacy_en" rows="6" 
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" 
                            dir="ltr">{{ old('privacy_en', $privacyEn ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('privacy_en')" />
                    </div>
                </div>
            </div>

            <!-- Social Media Settings Section -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-6">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">إعدادات وسائل التواصل الاجتماعي</h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="whatsapp_link" value="رابط واتساب" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-whatsapp text-slate-400"></i>
                            </div>
                            <x-text-input id="whatsapp_link" name="whatsapp_link" type="url" :value="old('whatsapp_link', $whatsappLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('whatsapp_link')" />
                    </div>

                    <div>
                        <x-input-label for="whatsapp_number" value="رقم واتساب (بدون +)" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-phone text-slate-400"></i>
                            </div>
                            <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" :value="old('whatsapp_number', $whatsappNumber)" class="pl-10 text-left" dir="ltr" placeholder="963991195136" />
                        </div>
                        <p class="mt-1 text-xs text-slate-500">يستخدم لإنشاء رابط المحادثة المباشرة (wa.me)</p>
                        <x-input-error :messages="$errors->get('whatsapp_number')" />
                    </div>

                    <div>
                        <x-input-label for="instagram_link" value="رابط انستغرام" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-instagram text-slate-400"></i>
                            </div>
                            <x-text-input id="instagram_link" name="instagram_link" type="url" :value="old('instagram_link', $instagramLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('instagram_link')" />
                    </div>

                    <div>
                        <x-input-label for="telegram_link" value="رابط تيليجرام" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-brands fa-telegram text-slate-400"></i>
                            </div>
                            <x-text-input id="telegram_link" name="telegram_link" type="url" :value="old('telegram_link', $telegramLink)" class="pl-10 text-left" dir="ltr" />
                        </div>
                        <x-input-error :messages="$errors->get('telegram_link')" />
                    </div>

                    <div>
                        <x-input-label for="facebook_link" value="رابط فيسبوك" />
                        <x-text-input id="facebook_link" name="facebook_link" type="url" class="mt-1 block w-full" :value="old('facebook_link', $facebookLink)" placeholder="https://facebook.com/..." />
                        <x-input-error :messages="$errors->get('facebook_link')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="upscroll_link" value="رابط Upscroll" />
                        <x-text-input id="upscroll_link" name="upscroll_link" type="url" class="mt-1 block w-full" :value="old('upscroll_link', $upscrollLink)" placeholder="https://..." />
                        <x-input-error :messages="$errors->get('upscroll_link')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-3">
                <x-primary-button>حفظ التغييرات</x-primary-button>
                <a href="{{ route('admin.index') }}" class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:border-emerald-200 dark:hover:border-emerald-500">
                    {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleLogoInputs(type) {
            const textInput = document.getElementById('logo_text_input');
            const imageInput = document.getElementById('logo_image_input');
            
            if (type === 'text') {
                textInput.style.display = 'block';
                imageInput.style.display = 'none';
            } else {
                textInput.style.display = 'none';
                imageInput.style.display = 'block';
            }
        }
    </script>
@endsection
