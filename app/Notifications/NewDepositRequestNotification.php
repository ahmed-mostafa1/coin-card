<?php

namespace App\Notifications;

use App\Models\DepositRequest;
use Illuminate\Notifications\Notification;

class NewDepositRequestNotification extends Notification
{
    public function __construct(private readonly DepositRequest $deposit)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $amountText = number_format($this->deposit->user_amount, 2) . ' USD';

        return [
            'title' => 'messages.notifications_custom.new_deposit_request_title',
            'description' => 'messages.notifications_custom.new_deposit_request_desc',
            'title_params' => [],
            'description_params' => [
                'user' => $this->deposit->user->name,
                'amount' => $amountText,
            ],
            'url' => route('admin.deposits.show', $this->deposit),
            'deposit_id' => $this->deposit->id,
            'user_id' => $this->deposit->user_id,
            'user_name' => $this->deposit->user->name,
            'amount' => $this->deposit->user_amount,
            'status' => $this->deposit->status,
        ];
    }
}
