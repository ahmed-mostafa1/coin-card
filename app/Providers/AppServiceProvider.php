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
            
            $sharedLogoType = Cache::remember('shared_logo_type', 300, fn () => SiteSetting::get('logo_type', 'text'));
            $sharedLogoText = Cache::remember('shared_logo_text', 300, fn () => SiteSetting::get('logo_text', 'Arab 8BP.in'));
            $sharedLogoImage = Cache::remember('shared_logo_image', 300, fn () => SiteSetting::get('logo_image', null));
            $sharedUpscrollImage = Cache::remember('shared_upscroll_image', 300, fn () => SiteSetting::get('upscroll_image', null));
            $sharedStoreDescription = Cache::remember('shared_store_description', 300, fn () => SiteSetting::get('store_description', 'متجر عربي متخصص في بيع بطاقات الألعاب والخدمات الرقمية بأسعار تنافسية وجودة عالية. نحن نقدم خدمة سريعة وموثوقة لجميع عملائنا. للاستفسارات أو الدعم، يرجى'));
            
            $activePopups = Cache::remember('active_popups', 300, function () {
                return \App\Models\Popup::where('is_active', true)
                    ->orderBy('display_order')
                    ->get()
                    ->append(['localized_title', 'localized_content']);
            });

            $sharedTickerTextEn = Cache::remember('shared_ticker_en', 300, fn () => SiteSetting::get('ticker_text_en', ''));
            
            $sharedWhatsappLink = Cache::remember('shared_whatsapp_link', 300, fn () => SiteSetting::get('whatsapp_link', 'https://wa.me/963991195136'));
            $sharedInstagramLink = Cache::remember('shared_instagram_link', 300, fn () => SiteSetting::get('instagram_link', '#'));
            $sharedTelegramLink = Cache::remember('shared_telegram_link', 300, fn () => SiteSetting::get('telegram_link', '#'));
            $sharedFacebookLink = Cache::remember('shared_facebook_link', 300, fn () => SiteSetting::get('facebook_link', '#'));
        } catch (\Throwable $e) {
            $sharedBanners = collect();
            $sharedTickerText = 'ملاحظة لأصحاب المحلات يرجى التواصل مع الإدارة للحصول على أسعار الجملة •';
            $sharedLogoType = 'text';
            $sharedLogoText = 'Arab 8BP.in';
            $sharedLogoImage = null;
            $sharedUpscrollImage = null;
            $sharedStoreDescription = 'متجر عربي متخصص في بيع بطاقات الألعاب والخدمات الرقمية بأسعار تنافسية وجودة عالية. نحن نقدم خدمة سريعة وموثوقة لجميع عملائنا. للاستفسارات أو الدعم، يرجى';
            $activePopups = collect();
            $sharedTickerTextEn = '';
            $sharedWhatsappLink = 'https://wa.me/963991195136';
            $sharedInstagramLink = '#';
            $sharedTelegramLink = '#';
            $sharedFacebookLink = '#';
        }

        View::share('sharedBanners', $sharedBanners);
        View::share('sharedTickerText', $sharedTickerText);
        View::share('sharedLogoType', $sharedLogoType);
        View::share('sharedLogoText', $sharedLogoText);
        View::share('sharedLogoImage', $sharedLogoImage);
        View::share('sharedUpscrollImage', $sharedUpscrollImage);
        View::share('sharedStoreDescription', $sharedStoreDescription);
        View::share('activePopups', $activePopups);
        
        View::share('sharedTickerTextEn', $sharedTickerTextEn);
        View::share('sharedWhatsappLink', $sharedWhatsappLink);
        View::share('sharedInstagramLink', $sharedInstagramLink);
        View::share('sharedTelegramLink', $sharedTelegramLink);
        View::share('sharedFacebookLink', $sharedFacebookLink);

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
