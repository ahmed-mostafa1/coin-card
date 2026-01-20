<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.services.create', compact('categories'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $this->prepareData($request);

        Service::create($data);

        return redirect()->route('admin.services.index')
            ->with('status', 'تم إضافة الخدمة بنجاح.');
    }

    public function edit(Service $service): View
    {
        $service->load(['formFields.options' => fn ($query) => $query->orderBy('sort_order')]);
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $this->prepareData($request, $service);

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }

            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'تم تحديث الخدمة بنجاح.');
    }

    private function prepareData(ServiceRequest $request, ?Service $service = null): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $slug = $data['slug'] ?? null;
        if (! $slug) {
            $slug = Str::slug($data['name']);
        }
        if (! $slug) {
            $slug = Str::random(8);
        }
        $baseSlug = $slug;
        $counter = 1;
        while (Service::where('slug', $slug)->when($service, fn ($q) => $q->where('id', '!=', $service->id))->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('image') && ! $service) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        return $data;
    }
}
