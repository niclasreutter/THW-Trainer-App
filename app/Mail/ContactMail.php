<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ContactMessage $contactMessage
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $typeLabels = [
            'feedback' => '💭 Feedback',
            'feature' => '✨ Feature-Wunsch',
            'bug' => '🐛 Fehler melden',
            'other' => '📧 Sonstiges',
        ];

        $subject = '[THW-Trainer] ' . ($typeLabels[$this->contactMessage->type] ?? 'Kontakt');

        return new Envelope(
            subject: $subject,
            replyTo: [$this->contactMessage->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
