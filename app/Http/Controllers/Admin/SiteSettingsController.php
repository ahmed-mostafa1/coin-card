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
        $tickerText = SiteSetting::get('ticker_text', 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •');
        $tickerTextEn = SiteSetting::get('ticker_text_en', '');
        $logoType = SiteSetting::get('logo_type', 'text'); // 'text' or 'image'
        $logoText = SiteSetting::get('logo_text', 'Arab 8BP.in');
        $logoImage = SiteSetting::get('logo_image', null);
        $upscrollLink = SiteSetting::get('upscroll_link', '#');
        $aboutAr = SiteSetting::get('about_ar', '');
        $aboutEn = SiteSetting::get('about_en', '');
        $privacyAr = SiteSetting::get('privacy_ar', '');
        $privacyEn = SiteSetting::get('privacy_en', '');
        $storeDescription = SiteSetting::get('store_description', 'متجر عربي متخصص في بيع بطاقات الألعاب والخدمات الرقمية بأسعار تنافسية وجودة عالية. نحن نقدم خدمة سريعة وموثوقة لجميع عملائنا. للاستفسارات أو الدعم، يرجى');
        $storeDescriptionEn = SiteSetting::get('store_description_en', '');
        
        $whatsappLink = SiteSetting::get('whatsapp_link', 'https://wa.me/963991195136');
        $whatsappNumber = SiteSetting::get('whatsapp_number', '');
        $instagramLink = SiteSetting::get('instagram_link', '#');
        $telegramLink = SiteSetting::get('telegram_link', '#');
        $facebookLink = SiteSetting::get('facebook_link', '#');
        
        $activePopupsCount = \App\Models\Popup::active()->count();

        return view('admin.site-settings.edit', compact(
            'tickerText',
            'tickerTextEn',
            'logoType',
            'logoText',
            'logoText',
            'logoText',
            'logoImage',
            'upscrollLink',
            'storeDescription',
            'whatsappLink',
            'instagramLink',
            'telegramLink',
            'facebookLink',
            'activePopupsCount',
            'aboutAr', 'aboutEn', 'privacyAr', 'privacyEn'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticker_text' => ['required', 'string', 'max:500'],
            'ticker_text_en' => ['nullable', 'string', 'max:500'],
            'logo_type' => ['required', 'in:text,image'],
            'logo_text' => ['nullable', 'string', 'max:100'],
            'logo_image' => ['nullable', 'image', 'max:2048'], // 2MB max
            'store_description' => ['required', 'string', 'max:1000'],
            'store_description_en' => ['nullable', 'string', 'max:1000'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'whatsapp_link' => ['nullable', 'url'],
            'instagram_link' => ['nullable', 'url'],
            'telegram_link' => ['nullable', 'url'],
            'facebook_link' => ['nullable', 'url'],
            'upscroll_link' => ['nullable', 'url'],
            'about_ar' => ['nullable', 'string'],
            'about_en' => ['nullable', 'string'],
            'privacy_ar' => ['nullable', 'string'],
            'privacy_en' => ['nullable', 'string'],
        ]);

        // Update ticker text
        SiteSetting::set('ticker_text', $data['ticker_text']);
        SiteSetting::set('ticker_text_en', $data['ticker_text_en'] ?? '');

        // Update social links
        SiteSetting::set('whatsapp_link', $data['whatsapp_link']);
        SiteSetting::set('whatsapp_number', $data['whatsapp_number']);
        SiteSetting::set('instagram_link', $data['instagram_link']);
        SiteSetting::set('telegram_link', $data['telegram_link']);
        SiteSetting::set('facebook_link', $data['facebook_link']);

        // Update logo settings
        SiteSetting::set('logo_type', $data['logo_type']);

        if ($data['logo_type'] === 'text') {
            SiteSetting::set('logo_text', $data['logo_text'] ?? 'Arab 8BP.in');
        } elseif ($data['logo_type'] === 'image' && $request->hasFile('logo_image')) {
            // Delete old logo image if exists
            $oldLogoImage = SiteSetting::get('logo_image');
            if ($oldLogoImage && Storage::disk('public')->exists($oldLogoImage)) {
                Storage::disk('public')->delete($oldLogoImage);
            }

            // Store new logo image
            $path = $request->file('logo_image')->store('logos', 'public');
            SiteSetting::set('logo_image', $path);
        }

        // Update Upscroll Link
        SiteSetting::set('upscroll_link', $data['upscroll_link']);
        cache()->forget('shared_upscroll_link');

        // Update store description
        SiteSetting::set('store_description', $data['store_description']);
        SiteSetting::set('store_description_en', $data['store_description_en'] ?? '');

        // Update Pages Content
        SiteSetting::set('about_ar', $data['about_ar'] ?? '');
        SiteSetting::set('about_en', $data['about_en'] ?? '');
        SiteSetting::set('privacy_ar', $data['privacy_ar'] ?? '');
        SiteSetting::set('privacy_en', $data['privacy_en'] ?? '');

        // Clear all related caches
        cache()->forget('shared_ticker');
        cache()->forget('shared_ticker_en');
        cache()->forget('shared_logo_type');
        cache()->forget('shared_logo_text');
        cache()->forget('shared_logo_image');
        cache()->forget('shared_store_description');
        cache()->forget('shared_store_description_en');
        
        cache()->forget('shared_whatsapp_link');
        cache()->forget('shared_whatsapp_number');
        cache()->forget('shared_instagram_link');
        cache()->forget('shared_telegram_link');
        cache()->forget('shared_facebook_link');

        cache()->forget('shared_about_ar');
        cache()->forget('shared_about_en');
        cache()->forget('shared_privacy_ar');
        cache()->forget('shared_privacy_en');

        return redirect()->route('admin.site-settings.edit')->with('status', 'تم تحديث إعدادات الموقع بنجاح.');
    }
}
