<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $search = request('q');

        $childrenBaseQuery = $category->children()->active();
        $hasChildren = $childrenBaseQuery->exists();

        $childrenQuery = (clone $childrenBaseQuery)
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($search) {
            $childrenQuery->where('name', 'like', "%{$search}%");
        }

        $subcategories = $childrenQuery->get();

        $services = collect();

        if (! $hasChildren) {
            $services = $category->services()
                ->where('is_active', true)
                ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->with(['variants', 'category'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('categories.show', [
            'category' => $category,
            'services' => $services,
            'subcategories' => $subcategories,
            'search' => $search,
            'hasChildren' => $hasChildren,
        ]);
    }
}
