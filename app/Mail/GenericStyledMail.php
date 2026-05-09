<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericStyledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public ?string $title = null,
        public array $introLines = [],
        public array $outroLines = [],
        public ?string $actionText = null,
        public ?string $actionUrl = null,
        public ?string $helperText = null,
        public ?string $fallbackUrl = null,
        public string $direction = 'ltr',
        // Bilingual support
        public ?string $arTitle = null,
        public ?string $enTitle = null,
        public ?array $arIntroLines = null,
        public ?array $enIntroLines = null,
        public ?array $arOutroLines = null,
        public ?array $enOutroLines = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.app',
            with: [
                'subject'      => $this->emailSubject,
                'title'        => $this->title,
                'introLines'   => $this->introLines,
                'outroLines'   => $this->outroLines,
                'actionText'   => $this->actionText,
                'actionUrl'    => $this->actionUrl,
                'helperText'   => $this->helperText,
                'fallbackUrl'  => $this->fallbackUrl,
                'direction'    => $this->direction,
                // Bilingual fields (null = fall back to shared fields in the component)
                'arTitle'      => $this->arTitle,
                'enTitle'      => $this->enTitle,
                'arIntroLines' => $this->arIntroLines,
                'enIntroLines' => $this->enIntroLines,
                'arOutroLines' => $this->arOutroLines,
                'enOutroLines' => $this->enOutroLines,
            ],
        );
    }
}