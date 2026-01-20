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
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $status = $this->newStatus ?? $this->order->status;
        $title = match ($status) {
            Order::STATUS_PROCESSING => 'تم بدء تنفيذ الطلب',
            Order::STATUS_DONE => 'تم تنفيذ الطلب',
            Order::STATUS_REJECTED => 'تم رفض الطلب',
            default => 'تم إنشاء الطلب',
        };

        $amountText = number_format($this->order->amount_held, 2).' ر.س';
        $description = 'طلب #'.$this->order->id.' ('.$this->order->service->name.') بمبلغ '.$amountText.'.';

        return [
            'title' => $title,
            'description' => $description,
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
