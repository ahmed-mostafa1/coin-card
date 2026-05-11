<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function edit(): View
    {
        $homeFeatureSettings = collect(SiteSetting::homeFeatureDefaults())
            ->map(function (array $feature, int $index): array {
                return [
                    'index' => $index,
                    'icon' => $feature['icon'],
                    'title_ar' => SiteSetting::get("home_feature_{$index}_title_ar", $feature['title_ar']),
                    'title_en' => SiteSetting::get("home_feature_{$index}_title_en", $feature['title_en']),
                    'description_ar' => SiteSetting::get("home_feature_{$index}_description_ar", $feature['description_ar']),
                    'description_en' => SiteSetting::get("home_feature_{$index}_description_en", $feature['description_en']),
                ];
            })
            ->values();
        $homeHeroDefaults = SiteSetting::homeHeroDefaults();
        $homeHeroTitleAr = SiteSetting::get('home_hero_title_ar', $homeHeroDefaults['title_ar']);
        $homeHeroTitleEn = SiteSetting::get('home_hero_title_en', $homeHeroDefaults['title_en']);
        $homeHeroTextAr = SiteSetting::get('home_hero_text_ar', $homeHeroDefaults['description_ar']);
        $homeHeroTextEn = SiteSetting::get('home_hero_text_en', $homeHeroDefaults['description_en']);
        $tickerText = SiteSetting::get('ticker_text', 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •');
        $tickerTextEn = SiteSetting::get('ticker_text_en', '');
        $logoType = SiteSetting::get('logo_type', 'text'); // 'text' or 'image'
        $logoText = SiteSetting::get('logo_text', 'S7SH.com|شحنك شات.in');
        $logoImage = SiteSetting::get('logo_image', null);
        $youtubeLink = SiteSetting::get('youtube_link', '#');
        $aboutAr = SiteSetting::get('about_ar', '');
        $aboutEn = SiteSetting::get('about_en', '');
        $privacyAr = SiteSetting::get('privacy_ar', '');
        $privacyEn = SiteSetting::get('privacy_en', '');
        $storeDescription = SiteSetting::get('store_description', 'متجر عربي متخصص في بيع بطاقات الألعاب والخدمات الرقمية بأسعار تنافسية وجودة عالية. نحن نقدم خدمة سريعة وموثوقة لجميع عملائنا. للاستفسارات أو الدعم، يرجى');
        $storeDescriptionEn = SiteSetting::get('store_description_en', '');

        $whatsappLink = SiteSetting::get('whatsapp_link', 'https://wa.me/963991195136');
        $whatsappEnabled = SiteSetting::get('whatsapp_enabled', '1');
        $telegramLink = SiteSetting::get('telegram_link', '#');
        $telegramEnabled = SiteSetting::get('telegram_enabled', '1');
        $facebookLink = SiteSetting::get('facebook_link', '#');
        $facebookEnabled = SiteSetting::get('facebook_enabled', '1');
        $youtubeLink = SiteSetting::get('youtube_link', '#');
        $youtubeEnabled = SiteSetting::get('youtube_enabled', '1');
        $tiktokLink = SiteSetting::get('tiktok_link', '#');
        $tiktokEnabled = SiteSetting::get('tiktok_enabled', '1');

        $activePopupsCount = \App\Models\Popup::active()->count();

        $metaDescription = SiteSetting::get('meta_description', '');
        $metaKeywords = SiteSetting::get('meta_keywords', '');
        $seoTitle = SiteSetting::get('seo_title', '');
        $fbPixelId = SiteSetting::get('fb_pixel_id', '');
        $gaId = SiteSetting::get('ga_id', '');
        $headScripts = SiteSetting::get('head_scripts', '');
        $bodyScripts = SiteSetting::get('body_scripts', '');

        $maintenanceEnabled = SiteSetting::get('maintenance_enabled', '0');
        $maintenanceMessage = SiteSetting::get('maintenance_message', 'المتجر تحت الصيانة حالياً. سنعود قريباً!');
        $maintenanceImage = SiteSetting::get('maintenance_image', null);
        $maintenanceButtonText = SiteSetting::get('maintenance_button_text', '');
        $maintenanceButtonUrl = SiteSetting::get('maintenance_button_url', '');

        return view('admin.site-settings.edit', compact(
            'tickerText',
            'tickerTextEn',
            'logoType',
            'logoText',
            'logoText',
            'logoText',
            'logoImage',
            'youtubeLink',
            'storeDescription',
            'storeDescriptionEn',
            'whatsappLink',
            'whatsappEnabled',
            'telegramLink',
            'telegramEnabled',
            'facebookLink',
            'facebookEnabled',
            'youtubeLink',
            'youtubeEnabled',
            'tiktokLink',
            'tiktokEnabled',
            'activePopupsCount',
            'homeHeroTitleAr', 'homeHeroTitleEn', 'homeHeroTextAr', 'homeHeroTextEn',
            'homeFeatureSettings',
            'aboutAr', 'aboutEn', 'privacyAr', 'privacyEn',
            'metaDescription', 'metaKeywords', 'seoTitle', 'fbPixelId', 'gaId', 'headScripts', 'bodyScripts',
            'maintenanceEnabled', 'maintenanceMessage', 'maintenanceImage', 'maintenanceButtonText', 'maintenanceButtonUrl',
            'globalLoaderEnabled'
        ));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticker_text' => ['required', 'string', 'max:500'],
            'ticker_text_en' => ['nullable', 'string', 'max:500'],
            'store_description' => ['required', 'string', 'max:1000'],
            'store_description_en' => ['nullable', 'string', 'max:1000'],
            'home_hero_title_ar' => ['required', 'string', 'max:200'],
            'home_hero_title_en' => ['nullable', 'string', 'max:200'],
            'home_hero_text_ar' => ['required', 'string', 'max:1000'],
            'home_hero_text_en' => ['nullable', 'string', 'max:1000'],
            'home_feature_1_title_ar' => ['required', 'string', 'max:120'],
            'home_feature_1_title_en' => ['nullable', 'string', 'max:120'],
            'home_feature_1_description_ar' => ['required', 'string', 'max:500'],
            'home_feature_1_description_en' => ['nullable', 'string', 'max:500'],
            'home_feature_2_title_ar' => ['required', 'string', 'max:120'],
            'home_feature_2_title_en' => ['nullable', 'string', 'max:120'],
            'home_feature_2_description_ar' => ['required', 'string', 'max:500'],
            'home_feature_2_description_en' => ['nullable', 'string', 'max:500'],
            'home_feature_3_title_ar' => ['required', 'string', 'max:120'],
            'home_feature_3_title_en' => ['nullable', 'string', 'max:120'],
            'home_feature_3_description_ar' => ['required', 'string', 'max:500'],
            'home_feature_3_description_en' => ['nullable', 'string', 'max:500'],
            'home_feature_4_title_ar' => ['required', 'string', 'max:120'],
            'home_feature_4_title_en' => ['nullable', 'string', 'max:120'],
            'home_feature_4_description_ar' => ['required', 'string', 'max:500'],
            'home_feature_4_description_en' => ['nullable', 'string', 'max:500'],
            'global_loader_enabled' => ['nullable', 'boolean'],
        ]);

        SiteSetting::set('ticker_text', $data['ticker_text']);
        SiteSetting::set('ticker_text_en', $data['ticker_text_en'] ?? '');
        SiteSetting::set('store_description', $data['store_description']);
        SiteSetting::set('store_description_en', $data['store_description_en'] ?? '');
        SiteSetting::set('global_loader_enabled', $request->boolean('global_loader_enabled') ? '1' : '0');
        SiteSetting::set('home_hero_title_ar', $data['home_hero_title_ar']);
        SiteSetting::set('home_hero_title_en', $data['home_hero_title_en'] ?? '');
        SiteSetting::set('home_hero_text_ar', $data['home_hero_text_ar']);
        SiteSetting::set('home_hero_text_en', $data['home_hero_text_en'] ?? '');

        foreach (array_keys(SiteSetting::homeFeatureDefaults()) as $index) {
            SiteSetting::set("home_feature_{$index}_title_ar", $data["home_feature_{$index}_title_ar"]);
            SiteSetting::set("home_feature_{$index}_title_en", $data["home_feature_{$index}_title_en"] ?? '');
            SiteSetting::set("home_feature_{$index}_description_ar", $data["home_feature_{$index}_description_ar"]);
            SiteSetting::set("home_feature_{$index}_description_en", $data["home_feature_{$index}_description_en"] ?? '');
        }

        cache()->forget('shared_ticker');
        cache()->forget('shared_ticker_en');
        cache()->forget('shared_store_description');
        cache()->forget('shared_store_description_en');

        return redirect()->route('admin.site-settings.edit')->with('status', 'تم تحديث الإعدادات العامة بنجاح.');
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'logo_type' => ['required', 'in:text,image'],
            'logo_text' => ['nullable', 'string', 'max:100'],
            'logo_image' => ['nullable', 'image', 'max:2048'],
        ]);

        SiteSetting::set('logo_type', $data['logo_type']);
        SiteSetting::set('logo_text', $data['logo_text'] ?? 'S7SH.com|شحنك شات.in');

        if ($data['logo_type'] === 'image' && $request->hasFile('logo_image')) {
            $oldLogoImage = SiteSetting::get('logo_image');
            if ($oldLogoImage && Storage::disk('public')->exists($oldLogoImage)) {
                Storage::disk('public')->delete($oldLogoImage);
            }
            $path = $request->file('logo_image')->store('logos', 'public');
            SiteSetting::set('logo_image', $path);
        }

        cache()->forget('shared_logo_type');
        cache()->forget('shared_logo_text');
        cache()->forget('shared_logo_image');

        return redirect()->route('admin.site-settings.edit')->with('status', 'تم تحديث الشعار بنجاح.');
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'whatsapp_link' => ['nullable', 'string'],
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'telegram_link' => ['nullable', 'string'],
            'telegram_enabled' => ['nullable', 'boolean'],
            'facebook_link' => ['nullable', 'string'],
            'facebook_enabled' => ['nullable', 'boolean'],
            'youtube_link' => ['nullable', 'string'],
            'youtube_enabled' => ['nullable', 'boolean'],
            'tiktok_link' => ['nullable', 'string'],
            'tiktok_enabled' => ['nullable', 'boolean'],
        ]);

        SiteSetting::set('whatsapp_link', trim((string) ($data['whatsapp_link'] ?? '')));
        SiteSetting::set('whatsapp_enabled', $request->boolean('whatsapp_enabled') ? '1' : '0');
        SiteSetting::set('telegram_link', trim((string) ($data['telegram_link'] ?? '')));
        SiteSetting::set('telegram_enabled', $request->boolean('telegram_enabled') ? '1' : '0');
        SiteSetting::set('facebook_link', trim((string) ($data['facebook_link'] ?? '')));
        SiteSetting::set('facebook_enabled', $request->boolean('facebook_enabled') ? '1' : '0');
        SiteSetting::set('youtube_link', trim((string) ($data['youtube_link'] ?? '')));
        SiteSetting::set('youtube_enabled', $request->boolean('youtube_enabled') ? '1' : '0');
        SiteSetting::set('tiktok_link', trim((string) ($data['tiktok_link'] ?? '')));
        SiteSetting::set('tiktok_enabled', $request->boolean('tiktok_enabled') ? '1' : '0');

        cache()->forget('shared_whatsapp_link');
        cache()->forget('shared_whatsapp_enabled');
        cache()->forget('shared_telegram_link');
        cache()->forget('shared_telegram_enabled');
        cache()->forget('shared_facebook_link');
        cache()->forget('shared_facebook_enabled');
        cache()->forget('shared_youtube_link');
        cache()->forget('shared_youtube_enabled');
        cache()->forget('shared_tiktok_link');
        cache()->forget('shared_tiktok_enabled');

        return redirect()->route('admin.site-settings.edit')->with('status', 'تم تحديث روابط التواصل بنجاح.');
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'fb_pixel_id' => ['nullable', 'string', 'max:50', 'regex:/^\d*$/'],
            'ga_id' => ['nullable', 'string', 'max:30', 'regex:/^(G-|UA-)?[A-Z0-9\-]*$/i'],
            'head_scripts' => ['nullable', 'string'],
            'body_scripts' => ['nullable', 'string'],
        ]);

        SiteSetting::set('seo_title', trim($data['seo_title'] ?? ''));
        SiteSetting::set('meta_description', trim($data['meta_description'] ?? ''));
        SiteSetting::set('meta_keywords', trim($data['meta_keywords'] ?? ''));
        SiteSetting::set('fb_pixel_id', trim($data['fb_pixel_id'] ?? ''));
        SiteSetting::set('ga_id', trim($data['ga_id'] ?? ''));
        SiteSetting::set('head_scripts', trim($data['head_scripts'] ?? ''));
        SiteSetting::set('body_scripts', trim($data['body_scripts'] ?? ''));

        cache()->forget('shared_seo_title');
        cache()->forget('shared_meta_description');
        cache()->forget('shared_meta_keywords');
        cache()->forget('shared_fb_pixel_id');
        cache()->forget('shared_ga_id');
        cache()->forget('shared_head_scripts');
        cache()->forget('shared_body_scripts');

        return redirect()->route('admin.site-settings.edit')->with('status', 'تم تحديث إعدادات SEO والإعلانات بنجاح.');
    }

    public function updateMaintenance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'maintenance_enabled' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
            'maintenance_button_text' => ['nullable', 'string', 'max:100'],
            'maintenance_button_url' => ['nullable', 'string', 'max:500'],
            'maintenance_image' => ['nullable', 'image', 'max:2048'],
        ]);

        SiteSetting::set('maintenance_enabled', $request->boolean('maintenance_enabled') ? '1' : '0');
        SiteSetting::set('maintenance_message', $data['maintenance_message'] ?? 'المتجر تحت الصيانة حالياً. سنعود قريباً!');
        SiteSetting::set('maintenance_button_text', $data['maintenance_button_text'] ?? '');
        SiteSetting::set('maintenance_button_url', $data['maintenance_button_url'] ?? '');

        if ($request->hasFile('maintenance_image')) {
            $oldImage = SiteSetting::get('maintenance_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $path = $request->file('maintenance_image')->store('maintenance', 'public');
            SiteSetting::set('maintenance_image', $path);
        } elseif ($request->boolean('remove_maintenance_image')) {
            $oldImage = SiteSetting::get('maintenance_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            SiteSetting::set('maintenance_image', null);
        }

        cache()->forget('shared_maintenance_enabled');

        return redirect()->route('admin.site-settings.edit')->with('status', 'تم تحديث إعدادات الصيانة بنجاح.');
        }
        }
