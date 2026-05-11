<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\ResolvesNotificationChannels;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use ResolvesNotificationChannels;

    public function __construct(
        private readonly Order $order,
        private readonly ?string $oldStatus = null,
        private readonly ?string $newStatus = null
    ) {
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
        $subjectAr = __('messages.email_subjects.order_status_changed_user', ['app_name' => $appName], 'ar');
        $subjectEn = __('messages.email_subjects.order_status_changed_user', ['app_name' => $appName], 'en');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subjectAr . ' / ' . $subjectEn)
            ->view('emails.notifications.order_status_changed', [
                'order' => $this->order
            ]);
    }


    public function toDatabase(object $notifiable): array
    {
        $status = $this->newStatus ?? $this->order->status;
        $titleKey = match ($status) {
            Order::STATUS_PROCESSING => 'messages.notifications_custom.order_processing_title',
            Order::STATUS_DONE => 'messages.notifications_custom.order_done_title',
            Order::STATUS_REJECTED => 'messages.notifications_custom.order_rejected_title',
            default => 'messages.notifications_custom.order_created_title',
        };

        $amountText = number_format($this->order->amount_held, 2) . ' USD';

        return [
            'title' => $titleKey,
            'description' => 'messages.notifications_custom.order_desc',
            'title_params' => [],
            'description_params' => [
                'order_id' => $this->order->id,
                'service' => $this->order->service->name,
                'amount' => $amountText,
            ],
            'url' => route('account.orders.show', $this->order),
            'order_id' => $this->order->id,
            'service_id' => $this->order->service_id,
            'service_name' => $this->order->service->name,
            'status' => $status,
            'old_status' => $this->oldStatus,
            'new_status' => $status,
            'amount' => $this->order->amount_held,
        ];
    }
}
