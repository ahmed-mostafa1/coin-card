<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function edit(): View
    {
        $aboutAr = SiteSetting::get('about_ar', '');
        $aboutEn = SiteSetting::get('about_en', '');
        $privacyAr = SiteSetting::get('privacy_ar', '');
        $privacyEn = SiteSetting::get('privacy_en', '');
        $termsAr = SiteSetting::get('terms_ar', '');
        $termsEn = SiteSetting::get('terms_en', '');

        return view('admin.pages.edit', compact('aboutAr', 'aboutEn', 'privacyAr', 'privacyEn', 'termsAr', 'termsEn'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_ar' => ['nullable', 'string'],
            'about_en' => ['nullable', 'string'],
            'privacy_ar' => ['nullable', 'string'],
            'privacy_en' => ['nullable', 'string'],
            'terms_ar' => ['nullable', 'string'],
            'terms_en' => ['nullable', 'string'],
        ]);

        SiteSetting::set('about_ar', trim($data['about_ar'] ?? ''));
        SiteSetting::set('about_en', trim($data['about_en'] ?? ''));
        SiteSetting::set('privacy_ar', trim($data['privacy_ar'] ?? ''));
        SiteSetting::set('privacy_en', trim($data['privacy_en'] ?? ''));
        SiteSetting::set('terms_ar', trim($data['terms_ar'] ?? ''));
        SiteSetting::set('terms_en', trim($data['terms_en'] ?? ''));

        // Clear caches
        cache()->forget('shared_about_ar');
        cache()->forget('shared_about_en');
        cache()->forget('shared_privacy_ar');
        cache()->forget('shared_privacy_en');
        cache()->forget('shared_terms_ar');
        cache()->forget('shared_terms_en');

        return redirect()->route('admin.pages.edit')->with('status', 'تم تحديث صفحات المحتوى بنجاح.');
    }
}
