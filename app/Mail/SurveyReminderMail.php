<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurveyReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $surveyUrl;

    public function __construct(User $user, string $surveyUrl)
    {
        $this->user = $user;
        $this->surveyUrl = $surveyUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deine Meinung zählt – Hilf uns den THW Trainer zu verbessern!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.survey-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
