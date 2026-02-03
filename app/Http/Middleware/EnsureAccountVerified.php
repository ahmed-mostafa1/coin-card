<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->email_verified_at) {
             if ($request->expectsJson()) {
                 return response()->json(['message' => 'Your account is not verified.'], 403);
             }

             // Redirect to home and show popup
             session(['show_otp_verify' => true]);
             return redirect()->route('home')->with('error', __('messages.account_verification_required'));
        }

        return $next($request);
    }
}
