<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = \App\Models\Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $services = \App\Models\Service::query()
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('categories', 'services'));
    }
}
