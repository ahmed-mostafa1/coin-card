<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VipTier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VipTierController extends Controller
{
    public function index(): View
    {
        $tiers = VipTier::orderBy('rank')->get();
        return view('admin.vip-tiers.index', compact('tiers'));
    }

    public function create(): View
    {
        return view('admin.vip-tiers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'rank' => ['required', 'integer', 'min:0', 'unique:vip_tiers,rank'],
            'deposits_required' => ['required', 'numeric', 'min:0'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('vip-tiers', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        VipTier::create($data);

        return redirect()->route('admin.vip-tiers.index')->with('status', 'تم إضافة المستوى بنجاح.');
    }

    public function edit(VipTier $vipTier): View
    {
        return view('admin.vip-tiers.edit', compact('vipTier'));
    }

    public function update(Request $request, VipTier $vipTier): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'rank' => ['required', 'integer', 'min:0', 'unique:vip_tiers,rank,' . $vipTier->id],
            'deposits_required' => ['required', 'numeric', 'min:0'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image_path')) {
            if ($vipTier->image_path) {
                Storage::disk('public')->delete($vipTier->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('vip-tiers', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $vipTier->update($data);

        return redirect()->route('admin.vip-tiers.index')->with('status', 'تم تحديث المستوى بنجاح.');
    }

    public function destroy(VipTier $vipTier): RedirectResponse
    {
        if ($vipTier->image_path) {
            Storage::disk('public')->delete($vipTier->image_path);
        }
        
        $vipTier->delete();

        return redirect()->route('admin.vip-tiers.index')->with('status', 'تم حذف المستوى بنجاح.');
    }
}
