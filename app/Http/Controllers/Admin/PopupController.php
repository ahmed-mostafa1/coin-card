<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PopupController extends Controller
{
    public function index(): View
    {
        $popups = Popup::orderBy('display_order')->get();
        return view('admin.popups.index', compact('popups'));
    }

    public function create(): View
    {
        return view('admin.popups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
        ]);

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('popups', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['display_order'] = $request->display_order ?? 0;

        Popup::create($data);

        cache()->forget('active_popups');

        return redirect()->route('admin.popups.index')->with('status', 'تم إضافة النافذة بنجاح.');
    }

    public function edit(Popup $popup): View
    {
        return view('admin.popups.edit', compact('popup'));
    }

    public function update(Request $request, Popup $popup): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'image_path' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
        ]);

        if ($request->hasFile('image_path')) {
            if ($popup->image_path) {
                Storage::disk('public')->delete($popup->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('popups', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['display_order'] = $request->display_order ?? 0;

        $popup->update($data);

        cache()->forget('active_popups');

        return redirect()->route('admin.popups.index')->with('status', 'تم تحديث النافذة بنجاح.');
    }

    public function destroy(Popup $popup): RedirectResponse
    {
        if ($popup->image_path) {
            Storage::disk('public')->delete($popup->image_path);
        }
        
        $popup->delete();
        
        cache()->forget('active_popups');

        return redirect()->route('admin.popups.index')->with('status', 'تم حذف النافذة بنجاح.');
    }
}
