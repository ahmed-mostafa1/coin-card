<?php

namespace App\Notifications;

use App\Models\DepositRequest;
use Illuminate\Notifications\Notification;

class DepositStatusChangedNotification extends Notification
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
        $status = $this->deposit->status;
        $approvedAmount = $this->deposit->approved_amount;
        $amountText = $approvedAmount
            ? number_format($approvedAmount, 2).' ر.س'
            : number_format($this->deposit->user_amount, 2).' ر.س';

        $title = $status === DepositRequest::STATUS_APPROVED
            ? 'تم اعتماد طلب الشحن'
            : 'تم رفض طلب الشحن';

        $description = $status === DepositRequest::STATUS_APPROVED
            ? 'تم اعتماد طلب الشحن رقم #'.$this->deposit->id.' بمبلغ '.$amountText.'.'
            : 'تم رفض طلب الشحن رقم #'.$this->deposit->id.'.';

        if ($status === DepositRequest::STATUS_REJECTED && $this->deposit->admin_note) {
            $description .= ' السبب: '.$this->deposit->admin_note;
        }

        return [
            'title' => $title,
            'description' => $description,
            'url' => route('account.deposits.show', $this->deposit),
            'deposit_id' => $this->deposit->id,
            'status' => $status,
            'approved_amount' => $approvedAmount,
            'user_amount' => $this->deposit->user_amount,
        ];
    }
}
