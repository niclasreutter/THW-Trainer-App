<?php

namespace App\Mail;

use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Models\LearningSessionParticipant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LernsessionEndedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $session;
    public $instance;
    public $participant;
    public $isWinner;
    public $totalParticipants;
    public $hasLootbox;

    public function __construct(
        User $user,
        LearningSession $session,
        LearningSessionInstance $instance,
        LearningSessionParticipant $participant,
        bool $isWinner,
        int $totalParticipants,
        bool $hasLootbox
    ) {
        $this->user = $user;
        $this->session = $session;
        $this->instance = $instance;
        $this->participant = $participant;
        $this->isWinner = $isWinner;
        $this->totalParticipants = $totalParticipants;
        $this->hasLootbox = $hasLootbox;
    }

    public function envelope(): Envelope
    {
        $subject = $this->isWinner
            ? 'Lernsession gewonnen: ' . $this->session->title
            : 'Lernsession beendet: ' . $this->session->title;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lernsession-ended',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
