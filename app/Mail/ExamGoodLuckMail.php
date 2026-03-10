<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamGoodLuckMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deine Prüfung ist morgen - Letzte Tipps vom THW-Trainer',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-goodluck',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
