<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $daysLeft;
    public $dailyTarget;
    public $todayAnswered;
    public $todayRemaining;
    public $masteredCount;
    public $totalQuestions;
    public $progressPercent;

    public function __construct(User $user, array $data)
    {
        $this->user = $user;
        $this->daysLeft = $data['days_left'];
        $this->dailyTarget = $data['daily_target'];
        $this->todayAnswered = $data['today_answered'];
        $this->todayRemaining = max(0, $data['daily_target'] - $data['today_answered']);
        $this->masteredCount = $data['mastered_count'];
        $this->totalQuestions = $data['total_questions'];
        $this->progressPercent = $data['progress_percent'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Noch ' . $this->daysLeft . ' Tage bis zur Prüfung - Dein Tagesplan',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
