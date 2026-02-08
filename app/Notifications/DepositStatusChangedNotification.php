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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('تحديث حالة طلب الشحن - Coin7Card')
            ->view('emails.notifications.deposit_status_changed', [
                'deposit' => $this->deposit
            ]);
    }


    public function toDatabase(object $notifiable): array
    {
        $status = $this->deposit->status;
        $approvedAmount = $this->deposit->approved_amount;
        $amountText = $approvedAmount
            ? number_format($approvedAmount, 2) . ' USD'
            : number_format($this->deposit->user_amount, 2) . ' USD';

        $titleKey = $status === DepositRequest::STATUS_APPROVED
            ? 'messages.notifications_custom.deposit_approved_title'
            : 'messages.notifications_custom.deposit_rejected_title';

        $descriptionKey = $status === DepositRequest::STATUS_APPROVED
            ? 'messages.notifications_custom.deposit_approved_desc'
            : 'messages.notifications_custom.deposit_rejected_desc';

        $descParams = [
            'deposit_id' => $this->deposit->id,
            'amount' => $amountText
        ];

        return [
            'title' => $titleKey,
            'description' => $descriptionKey,
            'title_params' => [],
            'description_params' => $descParams,
            'admin_note' => $this->deposit->admin_note,
            'url' => route('account.deposits.show', $this->deposit),
            'deposit_id' => $this->deposit->id,
            'status' => $status,
            'approved_amount' => $approvedAmount,
            'user_amount' => $this->deposit->user_amount,
        ];
    }
}
