<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->roots()
            ->active()
            ->manual()
            ->withCount([
                'services' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $featuredServices = Service::query()
            ->with(['category', 'variants'])
            ->where('is_active', true)
            ->whereHas('category', function ($query): void {
                $query->active();
            })
            ->orderByDesc('is_offer_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return view('home', compact('categories', 'featuredServices'));
    }
}
