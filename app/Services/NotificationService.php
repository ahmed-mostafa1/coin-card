<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class NotificationService
{
    public function notifyAdmins(Notification $notification): void
    {
        try {
            $admins = User::role('admin')->get();
        } catch (RoleDoesNotExist) {
            return;
        }

        if ($admins->isEmpty()) {
            return;
        }

        NotificationFacade::send($admins, $notification);
    }
}
