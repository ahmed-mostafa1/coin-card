<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = \App\Models\Category::query()
            ->roots()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('home', compact('categories'));
    }
}
