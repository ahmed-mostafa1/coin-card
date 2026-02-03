<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    public function __construct(
        private readonly Order $order,
        private readonly ?string $oldStatus = null,
        private readonly ?string $newStatus = null
    ) {
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
        $data = $this->toDatabase($notifiable);
        $title = __($data['title'], $data['title_params'] ?? []);
        $description = __($data['description'], $data['description_params'] ?? []);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($title)
            ->view('emails.layout', [
                'title' => $title,
                'slot' => new \Illuminate\Support\HtmlString(nl2br(e($description)))
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
