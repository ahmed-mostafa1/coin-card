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

        return view('admin.pages.edit', compact('aboutAr', 'aboutEn', 'privacyAr', 'privacyEn'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_ar' => ['nullable', 'string'],
            'about_en' => ['nullable', 'string'],
            'privacy_ar' => ['nullable', 'string'],
            'privacy_en' => ['nullable', 'string'],
        ]);

        SiteSetting::set('about_ar', $data['about_ar'] ?? '');
        SiteSetting::set('about_en', $data['about_en'] ?? '');
        SiteSetting::set('privacy_ar', $data['privacy_ar'] ?? '');
        SiteSetting::set('privacy_en', $data['privacy_en'] ?? '');

        // Clear caches
        cache()->forget('shared_about_ar');
        cache()->forget('shared_about_en');
        cache()->forget('shared_privacy_ar');
        cache()->forget('shared_privacy_en');

        return redirect()->route('admin.pages.edit')->with('status', 'تم تحديث صفحات المحتوى بنجاح.');
    }
}
