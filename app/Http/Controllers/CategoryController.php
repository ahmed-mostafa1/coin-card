<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $services = $category->services()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('categories.show', compact('category', 'services'));
    }
}
