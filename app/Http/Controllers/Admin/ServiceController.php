<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::query()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('external_product_id', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $services = $query->paginate(20)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.services.create', compact('categories'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $this->prepareData($request);

        $service = DB::transaction(function () use ($request, $data) {
            $service = Service::create($data);

            foreach ($request->input('variants', []) as $index => $variant) {
                $service->variants()->create([
                    'name' => $variant['name'],
                    'price' => $variant['price'],
                    'is_active' => isset($variant['is_active']) ? (bool) $variant['is_active'] : true,
                    'sort_order' => $variant['sort_order'] ?? $index,
                ]);
            }

            foreach ($request->input('fields', []) as $index => $field) {
                $service->formFields()->create([
                    'type' => $field['type'],
                    'label' => $field['label'],
                    'label_en' => $field['label_en'] ?? null,
                    'name_key' => $field['name_key'],
                    'placeholder' => $field['placeholder'] ?? null,
                    'placeholder_en' => $field['placeholder_en'] ?? null,
                    'is_required' => isset($field['is_required']) ? (bool) $field['is_required'] : false,
                    'sort_order' => $field['sort_order'] ?? $index,
                ]);
            }

            return $service;
        });

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'تم إضافة الخدمة بنجاح.');
    }

    public function edit(Service $service): View
    {
        $service->load(['formFields.options' => fn($query) => $query->orderBy('sort_order')]);
        $categories = Category::query()
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

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

        if ($request->hasFile('offer_image')) {
            if ($service->offer_image_path) {
                Storage::disk('public')->delete($service->offer_image_path);
            }

            $data['offer_image_path'] = $request->file('offer_image')->store('services/offers', 'public');
        }

        $service->update($data);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'تم تحديث الخدمة بنجاح.');
    }

    private function prepareData(ServiceRequest $request, ?Service $service = null): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_offer_active'] = $request->boolean('is_offer_active');
        $data['is_limited_offer_label_active'] = $request->boolean('is_limited_offer_label_active');
        $data['is_limited_offer_countdown_active'] = $request->boolean('is_limited_offer_countdown_active');
        $data['is_quantity_based'] = $request->boolean('is_quantity_based');
        $data['source'] = $data['source'] ?? $service?->source ?? Service::SOURCE_MANUAL;
        $data['sync_rule_mode'] = $data['sync_rule_mode'] ?? $service?->sync_rule_mode ?? Service::SYNC_RULE_AUTO;
        if ($data['source'] === Service::SOURCE_MARKETCARD99 || $request->hasAny([
            'requires_customer_id',
            'requires_amount',
            'requires_purchase_password',
        ])) {
            $data['requires_customer_id'] = $request->boolean('requires_customer_id');
            $data['requires_amount'] = $request->boolean('requires_amount');
            $data['requires_purchase_password'] = $request->boolean('requires_purchase_password');
        }
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if (array_key_exists('limited_offer_label', $data)) {
            $data['limited_offer_label'] = trim((string) $data['limited_offer_label']) ?: null;
        }

        if (array_key_exists('limited_offer_label_en', $data)) {
            $data['limited_offer_label_en'] = trim((string) $data['limited_offer_label_en']) ?: null;
        }

        $slug = $data['slug'] ?? null;
        if (!$slug) {
            $slug = Str::slug($data['name']);
        }
        if (!$slug) {
            $slug = Str::random(8);
        }
        $baseSlug = $slug;
        $counter = 1;
        while (Service::where('slug', $slug)->when($service, fn($q) => $q->where('id', '!=', $service->id))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('image') && !$service) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        if ($request->hasFile('offer_image') && !$service) {
            $data['offer_image_path'] = $request->file('offer_image')->store('services/offers', 'public');
        }

        return $data;
    }
    public function destroy(Service $service): RedirectResponse
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }

        if ($service->offer_image_path) {
            Storage::disk('public')->delete($service->offer_image_path);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('status', 'تم حذف الخدمة بنجاح.');
    }
}
