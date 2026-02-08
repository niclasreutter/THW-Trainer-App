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
    public $masteredQuestions;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, int $daysInactive)
    {
        $this->user = $user;
        $this->daysInactive = $daysInactive;
        
        // Berechne Fragen-Statistiken
        $this->totalQuestions = Question::count();
        $this->masteredQuestions = count($user->solved_questions ?? []);
        $this->remainingQuestions = max(0, $this->totalQuestions - $this->masteredQuestions);

        $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();

        $totalProgress = 0;
        foreach ($progressData as $progress) {
            $totalProgress += min($progress->consecutive_correct, $threshold);
        }

        $maxProgress = $this->totalQuestions * $threshold;
        
        // Berechne Prozentsatz
        $this->progressPercentage = $maxProgress > 0 
            ? round(($totalProgress / $maxProgress) * 100) 
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

