<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericStyledMail extends Mailable implements ShouldQueue
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
        public string $direction = 'ltr'
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
            view: 'emails.layouts.app',
            with: [
                'subject' => $this->emailSubject,
                'title' => $this->title,
                'introLines' => $this->introLines,
                'outroLines' => $this->outroLines,
                'actionText' => $this->actionText,
                'actionUrl' => $this->actionUrl,
                'helperText' => $this->helperText,
                'fallbackUrl' => $this->fallbackUrl,
                'direction' => $this->direction,
            ],
        );
    }
}