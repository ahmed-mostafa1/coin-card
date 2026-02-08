<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserOrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تأكيد استلام الطلب - Arab 8bp.in')
            ->view('emails.notifications.user_order_created', ['order' => $this->order]);
    }

    public function toDatabase(object $notifiable): array
    {
        $amountText = number_format($this->order->amount_held, 2) . ' USD';

        return [
            'title' => 'messages.notifications_custom.order_created_title',
            'description' => 'messages.notifications_custom.order_created_desc',
            'title_params' => [],
            'description_params' => [
                'service' => $this->order->service->name,
                'amount' => $amountText,
            ],
            'url' => route('account.orders.show', $this->order),
            'order_id' => $this->order->id,
            'service_id' => $this->order->service_id,
            'service_name' => $this->order->service->name,
            'amount' => $this->order->amount_held,
            'status' => $this->order->status,
        ];
    }
}
