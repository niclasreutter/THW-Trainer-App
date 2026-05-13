<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IssueMentionedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $mentioned,
        public User $author,
        public int $issueId,
        public string $issueType,
        public ?string $questionText,
        public string $commentMessage,
        public string $issueUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[THW-Trainer] ' . $this->author->name . ' hat dich erwähnt',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.issue-mentioned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
