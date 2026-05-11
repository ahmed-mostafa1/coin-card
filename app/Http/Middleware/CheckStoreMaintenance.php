<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SiteSetting;

class CheckStoreMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenanceEnabled = cache()->remember('shared_maintenance_enabled', 3600, function () {
            return SiteSetting::get('maintenance_enabled', '0') === '1';
        });

        if ($isMaintenanceEnabled) {
            // Allow API routes and Webhooks
            if ($request->is('api/*')) {
                return $next($request);
            }

            // Allow login, logout, password resets
            if ($request->routeIs('login') || $request->routeIs('logout') || $request->routeIs('password.*')) {
                return $next($request);
            }

            // Allow admin routes and admins
            if ($request->is('admin*') || (auth()->check() && auth()->user()->hasRole('admin'))) {
                return $next($request);
            }

            // Allow maintenance route itself if it exists, or just return the view directly to avoid redirect loops
            // Actually, instead of returning a view, we can just return a response with the view
            if (! $request->routeIs('maintenance')) {
                // Return maintenance view directly
                return response()->view('maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
