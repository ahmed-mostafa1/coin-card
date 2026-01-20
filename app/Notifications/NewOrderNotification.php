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
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $amountText = number_format($this->order->amount_held, 2).' ر.س';

        return [
            'title' => 'طلب جديد',
            'description' => 'طلب جديد من '.$this->order->user->name.' لخدمة '.$this->order->service->name.' بمبلغ '.$amountText.'.',
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
