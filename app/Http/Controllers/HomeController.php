<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $homeHeroDefaults = SiteSetting::homeHeroDefaults();
        $homeHeroTitle = app()->getLocale() === 'en'
            ? SiteSetting::get('home_hero_title_en', $homeHeroDefaults['title_en'])
            : SiteSetting::get('home_hero_title_ar', $homeHeroDefaults['title_ar']);
        $homeHeroText = app()->getLocale() === 'en'
            ? SiteSetting::get('home_hero_text_en', $homeHeroDefaults['description_en'])
            : SiteSetting::get('home_hero_text_ar', $homeHeroDefaults['description_ar']);

        $homeFeatureCards = collect(SiteSetting::homeFeatureDefaults())
            ->map(function (array $feature, int $index): array {
                $title = app()->getLocale() === 'en'
                    ? SiteSetting::get("home_feature_{$index}_title_en", $feature['title_en'])
                    : SiteSetting::get("home_feature_{$index}_title_ar", $feature['title_ar']);

                $description = app()->getLocale() === 'en'
                    ? SiteSetting::get("home_feature_{$index}_description_en", $feature['description_en'])
                    : SiteSetting::get("home_feature_{$index}_description_ar", $feature['description_ar']);

                return [
                    'icon' => $feature['icon'],
                    'title' => $title,
                    'description' => $description,
                ];
            })
            ->values();

        $categories = Category::query()
            ->roots()
            ->active()
            ->manual()
            ->withCount([
                'services' => fn ($query) => $query->where('is_active', true)->providerAvailable(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('home', compact('categories', 'homeFeatureCards', 'homeHeroTitle', 'homeHeroText'));
    }
}
