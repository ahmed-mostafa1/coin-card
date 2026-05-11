<?php

namespace App\Notifications\Concerns;

trait ResolvesNotificationChannels
{
    /**
     * @return array<int, string>
     */
    protected function notificationChannels(): array
    {
        $channels = ['database'];

        if ((bool) config('mail.notifications.email_enabled', false)) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
