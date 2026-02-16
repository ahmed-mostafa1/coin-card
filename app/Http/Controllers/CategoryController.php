<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);
        abort_unless(($category->source ?? Category::SOURCE_MANUAL) === Category::SOURCE_MANUAL, 404);

        $search = request('q');

        $childrenBaseQuery = $category->children()->active()->manual();
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
                ->manual()
                // ->where('is_active', true) // Show all services including inactive
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
