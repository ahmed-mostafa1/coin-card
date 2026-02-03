<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        // Determine locale based on notifiable preference or default to bilingual
        $isAr = app()->getLocale() == 'ar';
        
        // Use notifiable preference if available? For now, we'll format the email to show the relevant language or both.
        // Let's assume we send based on the current app locale if triggered individually, 
        // but for bulk, usually we want to respect user's locale.
        // However, `toMail` is called per user. "notifiable" is the user.
        // If we want to be smart:
        // $locale = $notifiable->locale ?? 'en'; 
        // But we don't know if user has 'locale' column.
        
        // Simple approach: Send both or just the title/content in user's preferred language?
        // Requirement: "text content only (title and content) in both languages arabic and english".
        // This suggests the content itself contains both.
        
        $subject = $this->titleEn . ' / ' . $this->titleAr;

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.layout', [
                'title' => $subject,
                'slot' => new \Illuminate\Support\HtmlString(
                    '<div style="text-align: left; direction: ltr; margin-bottom: 20px;">' . nl2br(e($this->contentEn)) . '</div>' .
                    '<hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">' .
                    '<div style="text-align: right; direction: rtl;">' . nl2br(e($this->contentAr)) . '</div>'
                )
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
