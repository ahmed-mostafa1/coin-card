<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    public function __construct(private readonly Order $order)
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
        $data = $this->toDatabase($notifiable);
        $title = __($data['title'], $data['title_params'] ?? []);
        $description = __($data['description'], $data['description_params'] ?? []);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($title)
            ->view('emails.layout', [
                'title' => $title,
                'slot' => new \Illuminate\Support\HtmlString(nl2br(e($description)) . '<br><br><a href="' . $data['url'] . '">View Order</a>')
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        $amountText = number_format($this->order->amount_held, 2) . ' USD';

        return [
            'title' => 'messages.notifications_custom.new_order_title',
            'description' => 'messages.notifications_custom.new_order_desc',
            'title_params' => [],
            'description_params' => [
                'user' => $this->order->user->name,
                'service' => $this->order->service->name,
                'amount' => $amountText,
            ],
            'url' => route('admin.orders.show', $this->order),
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'user_name' => $this->order->user->name,
            'service_id' => $this->order->service_id,
            'service_name' => $this->order->service->name,
            'amount' => $this->order->amount_held,
            'status' => $this->order->status,
        ];
    }
}
