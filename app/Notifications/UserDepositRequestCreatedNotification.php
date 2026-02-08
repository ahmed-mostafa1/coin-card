<?php

namespace App\Notifications;

use App\Models\DepositRequest;
use Illuminate\Notifications\Notification;

class UserDepositRequestCreatedNotification extends Notification
{
    public function __construct(private readonly DepositRequest $deposit)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('تم استلام طلب الشحن - Arab 8bp.in')
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
