<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceButton;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceButtonController extends Controller
{
    public function create(Service $service): View
    {
        return view('admin.services.buttons.create', compact('service'));
    }

    public function store(Service $service, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label_ar'   => ['required', 'string', 'max:120'],
            'label_en'   => ['nullable', 'string', 'max:120'],
            'url'        => ['required', 'url', 'max:500'],
            'bg_color'   => ['required', 'regex:/^#[0-9a-fA-F]{3,6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $service->buttons()->create($data);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', __('messages.button_created'));
    }

    public function edit(Service $service, ServiceButton $button): View
    {
        abort_if($button->service_id !== $service->id, 404);

        return view('admin.services.buttons.edit', compact('service', 'button'));
    }

    public function update(Service $service, ServiceButton $button, Request $request): RedirectResponse
    {
        abort_if($button->service_id !== $service->id, 404);

        $data = $request->validate([
            'label_ar'   => ['required', 'string', 'max:120'],
            'label_en'   => ['nullable', 'string', 'max:120'],
            'url'        => ['required', 'url', 'max:500'],
            'bg_color'   => ['required', 'regex:/^#[0-9a-fA-F]{3,6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $button->update($data);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', __('messages.button_updated'));
    }

    public function destroy(Service $service, ServiceButton $button): RedirectResponse
    {
        abort_if($button->service_id !== $service->id, 404);

        $button->delete();

        return redirect()->route('admin.services.edit', $service)
            ->with('status', __('messages.button_deleted'));
    }
}
