<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageToAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name:string,email:string,subject:string,message:string}  $payload
     */
    public function __construct(
        public array $payload,
        public ?string $recipientName = null,
        public ?User $submittedBy = null,
    ) {
    }

    public function envelope(): Envelope
    {
        $appName = config('app.name', 'Coin Card');

        return new Envelope(
            subject: __('messages.email_subjects.contact_message_admin', ['app_name' => $appName]),
            replyTo: [
                new Address($this->payload['email'], $this->payload['name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message-to-admin',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
