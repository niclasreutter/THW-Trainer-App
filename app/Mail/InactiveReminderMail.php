<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactiveReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $daysInactive;
    public $remainingQuestions;
    public $totalQuestions;
    public $progressPercentage;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, int $daysInactive)
    {
        $this->user = $user;
        $this->daysInactive = $daysInactive;
        
        // Berechne verbleibende Fragen
        $this->totalQuestions = Question::count();
        $solvedQuestions = count($user->solved_questions ?? []);
        $this->remainingQuestions = max(0, $this->totalQuestions - $solvedQuestions);
        $this->progressPercentage = $this->totalQuestions > 0 
            ? round(($solvedQuestions / $this->totalQuestions) * 100) 
            : 0;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->remainingQuestions > 0 
            ? 'Du fehlst uns! Nur noch ' . $this->remainingQuestions . ' Fragen bis zum Ziel 🎯'
            : 'Du fehlst uns! Bleib dran mit deinem Wissen 💪';
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inactive-reminder',
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

