<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserOrderCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'Arab 8bp.in');
        $subjectAr = __('messages.email_subjects.order_created_user', ['app_name' => $appName], 'ar');
        $subjectEn = __('messages.email_subjects.order_created_user', ['app_name' => $appName], 'en');

        return (new MailMessage)
            ->subject($subjectAr . ' / ' . $subjectEn)
            ->view('emails.notifications.user_order_created', [
                'order' => $this->order,
                'user' => $notifiable,
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        $amountText = number_format($this->order->price_at_purchase, 2) . ' ' . ($this->order->currency ?? 'USD');

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
        ];
    }
}
