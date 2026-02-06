<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpacedRepetitionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $dueCount;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, int $dueCount)
    {
        $this->user = $user;
        $this->dueCount = $dueCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->dueCount . ' Fragen warten auf deine Wiederholung!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.spaced-repetition-reminder',
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
