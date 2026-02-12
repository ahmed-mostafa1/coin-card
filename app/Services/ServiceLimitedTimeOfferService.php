<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Carbon;

class ServiceLimitedTimeOfferService
{
    public function deactivateExpiredServices(?Carbon $referenceTime = null): int
    {
        $referenceTime ??= now();

        return Service::query()
            ->where('is_active', true)
            ->where('is_limited_offer_countdown_active', true)
            ->whereNotNull('limited_offer_ends_at')
            ->where('limited_offer_ends_at', '<=', $referenceTime)
            ->update([
                'is_active' => false,
                'is_limited_offer_countdown_active' => false,
                'updated_at' => $referenceTime,
            ]);
    }

    public function deactivateIfExpired(Service $service, ?Carbon $referenceTime = null): bool
    {
        $referenceTime ??= now();

        if (! $service->isLimitedOfferExpired($referenceTime)) {
            return false;
        }

        $updated = Service::query()
            ->whereKey($service->id)
            ->where('is_active', true)
            ->where('is_limited_offer_countdown_active', true)
            ->whereNotNull('limited_offer_ends_at')
            ->where('limited_offer_ends_at', '<=', $referenceTime)
            ->update([
                'is_active' => false,
                'is_limited_offer_countdown_active' => false,
                'updated_at' => $referenceTime,
            ]);

        if ($updated > 0) {
            $service->refresh();
            return true;
        }

        return false;
    }
}
