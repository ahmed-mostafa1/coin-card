<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parents'));
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $this->prepareData($request);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'تم إضافة التصنيف بنجاح.');
    }

    public function edit(Category $category): View
    {
        $parents = Category::query()
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $this->prepareData($request, $category);

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }

            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'تم تحديث التصنيف بنجاح.');
    }

    private function prepareData(CategoryRequest $request, ?Category $category = null): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['parent_id'] = $request->input('parent_id') ?: null;

        $slug = $data['slug'] ?? null;
        if (! $slug) {
            $slug = Str::slug($data['name']);
        }
        if (! $slug) {
            $slug = Str::random(8);
        }
        $baseSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->when($category, fn ($q) => $q->where('id', '!=', $category->id))->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('image') && ! $category) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        return $data;
    }
}
