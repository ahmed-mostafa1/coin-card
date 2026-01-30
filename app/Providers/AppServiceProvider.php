<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $sharedBanners = Cache::remember('shared_banners', 300, function () {
                return Banner::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            });

            $sharedTickerText = Cache::remember('shared_ticker', 300, fn () => SiteSetting::get('ticker_text', 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •'));
        } catch (\Throwable $e) {
            $sharedBanners = collect();
            $sharedTickerText = 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •';
        }

        View::share('sharedBanners', $sharedBanners);
        View::share('sharedTickerText', $sharedTickerText);

        View::composer('layouts.app', function ($view): void {
            if (auth()->check()) {
                $user = auth()->user();
                $view->with([
                    'navNotifications' => $user->notifications()->latest()->limit(10)->get(),
                    'navUnreadCount' => $user->unreadNotifications()->count(),
                ]);
            }
        });
    }
}
