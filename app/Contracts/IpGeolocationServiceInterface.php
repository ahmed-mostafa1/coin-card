<?php

namespace App\Contracts;

interface IpGeolocationServiceInterface
{
    /**
     * @return array{country_code:?string,country:?string,city:?string}
     */
    public function lookup(?string $ip): array;
}
