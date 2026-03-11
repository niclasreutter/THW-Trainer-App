<?php

namespace App\Mail;

use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LernsessionStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $session;
    public $instance;

    public function __construct(User $user, LearningSession $session, LearningSessionInstance $instance)
    {
        $this->user = $user;
        $this->session = $session;
        $this->instance = $instance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lernsession gestartet: ' . $this->session->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lernsession-started',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
