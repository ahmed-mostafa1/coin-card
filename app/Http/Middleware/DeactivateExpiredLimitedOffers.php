<?php

namespace App\Http\Middleware;

use App\Services\ServiceLimitedTimeOfferService;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeactivateExpiredLimitedOffers
{
    public function __construct(private ServiceLimitedTimeOfferService $limitedTimeOfferService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->limitedTimeOfferService->deactivateExpiredServices();
        } catch (QueryException $exception) {
            $message = strtolower($exception->getMessage());

            if (! str_contains($message, 'limited_offer')) {
                throw $exception;
            }
        }

        return $next($request);
    }
}
