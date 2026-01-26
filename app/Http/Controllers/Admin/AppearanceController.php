<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppearanceController extends Controller
{
    public function edit(): View
    {
        $tickerText = SiteSetting::get('ticker_text', 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •');

        return view('admin.appearance.edit', compact('tickerText'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticker_text' => ['required', 'string', 'max:500'],
        ]);

        SiteSetting::set('ticker_text', $data['ticker_text']);

        return redirect()->route('admin.appearance.edit')->with('status', 'تم تحديث الإعدادات.');
    }
}
