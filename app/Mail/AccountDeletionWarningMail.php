<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->verificationUrl = route('verification.notice');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Dein THW-Trainer Account wird in 2 Tagen gelöscht',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion-warning',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'accountCreatedAt' => $this->user->created_at->format('d.m.Y H:i'),
            ]
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