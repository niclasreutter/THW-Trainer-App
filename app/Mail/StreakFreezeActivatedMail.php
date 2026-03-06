<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StreakFreezeActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $streakDays;
    public $freezesRemaining;

    public function __construct(User $user, int $streakDays, int $freezesRemaining)
    {
        $this->user = $user;
        $this->streakDays = $streakDays;
        $this->freezesRemaining = $freezesRemaining;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Streak Freeze aktiviert - Dein ' . $this->streakDays . '-Tage Streak ist geschützt!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.streak-freeze-activated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
