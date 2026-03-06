<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StreakLostMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $lostStreakDays;

    public function __construct(User $user, int $lostStreakDays)
    {
        $this->user = $user;
        $this->lostStreakDays = $lostStreakDays;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dein ' . $this->lostStreakDays . '-Tage Streak ist leider verloren gegangen',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.streak-lost',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
