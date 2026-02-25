<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceVariantRequest;
use App\Models\Service;
use App\Models\ServiceVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceVariantController extends Controller
{
    public function index(Service $service): View
    {
        $this->ensureVariantsSupported($service);

        $variants = $service->variants()->orderBy('sort_order')->get();

        return view('admin.services.variants.index', compact('service', 'variants'));
    }

    public function create(Service $service): View
    {
        $this->ensureVariantsSupported($service);

        return view('admin.services.variants.create', compact('service'));
    }

    public function store(ServiceVariantRequest $request, Service $service): RedirectResponse
    {
        $this->ensureVariantsSupported($service);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $service->variants()->create($data);

        return redirect()->route('admin.services.variants.index', $service)
            ->with('status', 'تم إضافة الباقة بنجاح.');
    }

    public function edit(Service $service, ServiceVariant $variant): View
    {
        $this->ensureVariantsSupported($service);

        if ($variant->service_id !== $service->id) {
            abort(404);
        }

        return view('admin.services.variants.edit', compact('service', 'variant'));
    }

    public function update(ServiceVariantRequest $request, Service $service, ServiceVariant $variant): RedirectResponse
    {
        $this->ensureVariantsSupported($service);

        if ($variant->service_id !== $service->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $variant->update($data);

        return redirect()->route('admin.services.variants.edit', [$service, $variant])
            ->with('status', 'تم تحديث الباقة بنجاح.');
    }

    public function destroy(Service $service, ServiceVariant $variant): RedirectResponse
    {
        $this->ensureVariantsSupported($service);

        if ($variant->service_id !== $service->id) {
            abort(404);
        }

        $variant->delete();

        return redirect()->route('admin.services.variants.index', $service)
            ->with('status', 'تم حذف الباقة بنجاح.');
    }

    private function ensureVariantsSupported(Service $service): void
    {
        abort_if($service->isDiscountedInputPricing(), 404);
    }
}
