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
        $amountText = number_format($this->deposit->user_amount, 2).' USD';

        return [
            'title' => 'طلب شحن جديد',
            'description' => 'طلب شحن جديد من '.$this->deposit->user->name.' بمبلغ '.$amountText.'.',
            'url' => route('admin.deposits.show', $this->deposit),
            'deposit_id' => $this->deposit->id,
            'user_id' => $this->deposit->user_id,
            'user_name' => $this->deposit->user->name,
            'amount' => $this->deposit->user_amount,
            'status' => $this->deposit->status,
        ];
    }
}
