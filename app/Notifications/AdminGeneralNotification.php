<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminGeneralNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public string $titleAr;
    public string $titleEn;
    public string $contentAr;
    public string $contentEn;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $titleAr, string $titleEn, string $contentAr, string $contentEn)
    {
        $this->titleAr = $titleAr;
        $this->titleEn = $titleEn;
        $this->contentAr = $contentAr;
        $this->contentEn = $contentEn;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->titleAr . ' / ' . $this->titleEn;

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notifications.admin_general', [
                'titleAr' => $this->titleAr,
                'titleEn' => $this->titleEn,
                'contentAr' => $this->contentAr,
                'contentEn' => $this->contentEn,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'title' => $this->titleEn, // Default key
            'description' => $this->contentEn, // Default key
            'title_ar' => $this->titleAr,
            'title_en' => $this->titleEn,
            'content_ar' => $this->contentAr,
            'content_en' => $this->contentEn,
            'type' => 'admin_message',
            'icon' => 'fa-solid fa-bullhorn', // Icon for frontend
            'link' => null,
        ];
    }
}
