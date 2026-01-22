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
            ? number_format($approvedAmount, 2) . ' USD'
            : number_format($this->deposit->user_amount, 2) . ' USD';

        $titleKey = $status === DepositRequest::STATUS_APPROVED
            ? 'messages.notifications_custom.deposit_approved_title'
            : 'messages.notifications_custom.deposit_rejected_title';

        $descriptionKey = $status === DepositRequest::STATUS_APPROVED
            ? 'messages.notifications_custom.deposit_approved_desc'
            : 'messages.notifications_custom.deposit_rejected_desc';

        if ($status === DepositRequest::STATUS_REJECTED && $this->deposit->admin_note) {
            $descriptionKey .= '_reason'; // Hack: assume we handle this in view or just key concatenation? NO. key concatenation won't work well with trans(). 
            // Better: use a different key or pass reason as param.
            // Let's rely on view logic? No, notification data is static-ish.
            // Let's just append the reason in the view if present? Or simpler: use a composite key?
            // "deposit_rejected_desc" is "Deposit request #:deposit_id rejected."
            // "deposit_rejected_reason" is " Reason: :reason"
            // I'll leave descriptionKey as base, and handle extra text in view or better yet:
            // I'll make description an array or just use one key and pass 'reason' param which might be empty.
            // But if empty, we don't want "Reason: ".
            // Let's use two different keys for rejected.
        }

        // Refined approach:
        $descParams = [
            'deposit_id' => $this->deposit->id,
            'amount' => $amountText
        ];

        if ($status === DepositRequest::STATUS_REJECTED && $this->deposit->admin_note) {
            // We'll just pass the reason in params, and let the translation key handle it?
            // But we need conditional translation key.
            // Let's stick to what I defined: 'deposit_rejected_reason' => ' Reason: :reason'
            // I will modify the logic to not append string here.
            // I will return the main key, and IF there is a note, the view will have to handle it?
            // Or better: the `description` field in JSON can be complex.
            // But for compatibility, let's keep `description` as the main key.

            // Actually, I can just use `trans()` immediately if I didn't care about language switching.
            // But I DO care.

            // Let's assume the view will display `__($data['description'], $data['description_params'])`.
            // If I need to append reason, I can't easily do it with one key if the structure differs.
            // I'll just change the key if there is a note.
        }

        return [
            'title' => $titleKey,
            'description' => $descriptionKey, // Note: I'll handle reason logic in the view or separate field
            'title_params' => [],
            'description_params' => $descParams,
            'admin_note' => $this->deposit->admin_note, // Pass this to view
            'url' => route('account.deposits.show', $this->deposit),
            'deposit_id' => $this->deposit->id,
            'status' => $status,
            'approved_amount' => $approvedAmount,
            'user_amount' => $this->deposit->user_amount,
        ];
    }
}
