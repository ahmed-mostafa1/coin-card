<?php

namespace App\Providers;

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
        View::composer('layouts.app', function ($view): void {
            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();

            $view->with([
                'navNotifications' => $user->notifications()->latest()->limit(10)->get(),
                'navUnreadCount' => $user->unreadNotifications()->count(),
            ]);
        });
    }
}
