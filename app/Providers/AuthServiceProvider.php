<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\DepositRequest;
use App\Policies\DepositRequestPolicy;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        DepositRequest::class => DepositRequestPolicy::class,
        Order::class => OrderPolicy::class,
    ];
}
