<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StreakReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $streakDays;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, int $streakDays)
    {
        $this->user = $user;
        $this->streakDays = $streakDays;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address('noreply@thw-trainer.de', 'THW-Trainer'),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('support@thw-trainer.de', 'THW-Trainer Support'),
            ],
            subject: '🔥 Dein ' . $this->streakDays . '-Tage Streak ist in Gefahr!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.streak-reminder',
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

    /**
     * Get the message headers.
     */
    public function headers(): array
    {
        return [
            'X-Mailer' => 'THW-Trainer',
            'X-Priority' => '3',
            'List-Unsubscribe' => '<' . url('/profile') . '>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ];
    }
}
