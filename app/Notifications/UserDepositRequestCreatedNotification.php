<?php

namespace App\Notifications;

use App\Models\DepositRequest;
use App\Notifications\Concerns\ResolvesNotificationChannels;
use Illuminate\Notifications\Notification;

class UserDepositRequestCreatedNotification extends Notification
{
    use ResolvesNotificationChannels;

    public function __construct(private readonly DepositRequest $deposit)
    {
    }

    public function via(object $notifiable): array
    {
        return $this->notificationChannels();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $appName = config('app.name', 'S7SH.com|شحنك شات.in');
        $subjectAr = __('messages.email_subjects.deposit_request_created_user', ['app_name' => $appName], 'ar');
        $subjectEn = __('messages.email_subjects.deposit_request_created_user', ['app_name' => $appName], 'en');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subjectAr . ' / ' . $subjectEn)
            ->view('emails.notifications.user_deposit_created', [
                'deposit' => $this->deposit
            ]);
    }


    public function toDatabase(object $notifiable): array
    {
        $amountText = number_format($this->deposit->user_amount, 2) . ' USD';

        return [
            'title' => 'messages.notifications_custom.deposit_created_title',
            'description' => 'messages.notifications_custom.deposit_created_desc',
            'title_params' => [],
            'description_params' => [
                'amount' => $amountText,
            ],
            'url' => route('account.deposits.show', $this->deposit),
            'deposit_id' => $this->deposit->id,
            'amount' => $this->deposit->user_amount,
            'status' => $this->deposit->status,
        ];
    }
}
