<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltySettingController extends Controller
{
    public function edit(): View
    {
        $settings = LoyaltySetting::current();

        return view('admin.loyalty-settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'points_per_usd' => ['required', 'numeric', 'min:0'],
            'level_3_points' => ['required', 'integer', 'min:1'],
            'level_2_discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'level_3_discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'points_step' => ['required', 'integer', 'min:1'],
            'discount_step_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        LoyaltySetting::current()->update($data);

        return redirect()->route('admin.loyalty-settings.edit')->with('status', 'تم تحديث إعدادات الولاء.');
    }
}
