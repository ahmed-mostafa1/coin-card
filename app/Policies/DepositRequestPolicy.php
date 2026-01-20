<?php

namespace App\Policies;

use App\Models\DepositRequest;
use App\Models\User;

class DepositRequestPolicy
{
    public function view(User $user, DepositRequest $depositRequest): bool
    {
        return $depositRequest->user_id === $user->id;
    }
}
