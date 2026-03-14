<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamFeedbackRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $feedbackUrl;

    public function __construct(User $user, string $feedbackUrl)
    {
        $this->user = $user;
        $this->feedbackUrl = $feedbackUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wie lief deine Prüfung? Wir sind gespannt!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-feedback-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
