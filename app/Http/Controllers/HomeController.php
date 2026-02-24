<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Artesaos\SEOTools\Facades\SEOTools;

class HomeController extends Controller
{
    public function index(): View
    {
        SEOTools::setTitle(__('messages.home'));

        $categories = \App\Models\Category::query()
            ->roots()
            ->active()
            ->manual()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('home', compact('categories'));
    }
}
