<?php

namespace App\Services;

use App\Contracts\IpGeolocationServiceInterface;

class NullIpGeolocationService implements IpGeolocationServiceInterface
{
    public function lookup(?string $ip): array
    {
        return [
            'country_code' => null,
            'country' => null,
            'city' => null,
        ];
    }
}
