<?php

namespace App\Notifications;

use App\Models\WalletTransaction;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BalanceAdjustedNotification extends Notification
{
    public function __construct(
        private readonly WalletTransaction $transaction,
        private readonly string $direction,
        private readonly ?string $note = null,
        private readonly float|int|string|null $currentBalance = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subjectKey = $this->subjectKey();
        $subjectAr = __($subjectKey, [], 'ar');
        $subjectEn = __($subjectKey, [], 'en');

        $amountText = $this->amountText();
        $balanceText = $this->balanceText();
        $descriptionKey = $this->descriptionKey();
        $params = [
            'amount' => $amountText,
            'balance' => $balanceText,
            'note' => $this->note,
        ];

        return (new MailMessage)
            ->subject($subjectAr . ' / ' . $subjectEn)
            // Arabic Content
            ->line(__('messages.balance_notification_greeting', ['name' => $notifiable->name], 'ar'))
            ->line(__($descriptionKey, $params, 'ar'))
            ->line('---')
            // English Content
            ->line(__('messages.balance_notification_greeting', ['name' => $notifiable->name], 'en'))
            ->line(__($descriptionKey, $params, 'en'))
            ->action(__('messages.view_wallet', [], 'ar') . ' / ' . __('messages.view_wallet', [], 'en'), route('account.wallet'));
    }

    public function toDatabase(object $notifiable): array
    {
        $amountValue = $this->amountValue();
        $amountText = $this->amountText();
        $balanceText = $this->balanceText();

        return [
            'title' => $this->titleKey(),
            'description' => $this->descriptionKey(),
            'title_params' => [],
            'description_params' => [
                'amount' => $amountText,
                'balance' => $balanceText,
                'note' => $this->note,
            ],
            'url' => route('account.wallet'),
            'transaction_id' => $this->transaction->id,
            'amount' => $amountValue,
            'direction' => $this->direction,
            'note' => $this->note,
            'balance' => $this->balanceValue(),
        ];
    }

    private function amountValue(): float
    {
        return abs((float) $this->transaction->amount);
    }

    private function amountText(): string
    {
        return number_format($this->amountValue(), 2) . ' USD';
    }

    private function balanceValue(): float
    {
        if ($this->currentBalance !== null) {
            return (float) $this->currentBalance;
        }

        if ($this->transaction->relationLoaded('wallet') && $this->transaction->wallet) {
            return (float) $this->transaction->wallet->balance;
        }

        return 0.0;
    }

    private function balanceText(): string
    {
        return number_format($this->balanceValue(), 2) . ' USD';
    }

    private function titleKey(): string
    {
        return $this->direction === 'debit'
            ? 'messages.notifications_custom.balance_debit_title'
            : 'messages.notifications_custom.balance_credit_title';
    }

    private function descriptionKey(): string
    {
        $hasNote = $this->note !== null && trim($this->note) !== '';

        if ($this->direction === 'debit') {
            return $hasNote
                ? 'messages.notifications_custom.balance_debit_desc_with_note'
                : 'messages.notifications_custom.balance_debit_desc';
        }

        return $hasNote
            ? 'messages.notifications_custom.balance_credit_desc_with_note'
            : 'messages.notifications_custom.balance_credit_desc';
    }

    private function subjectKey(): string
    {
        return $this->direction === 'debit'
            ? 'messages.balance_notification_subject_debit'
            : 'messages.balance_notification_subject_credit';
    }
}
