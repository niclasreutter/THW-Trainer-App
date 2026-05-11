<?php

namespace App\Mail;

use App\Models\UserExtraQuestionSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExtraQuestionSubmissionStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public UserExtraQuestionSubmission $submission
    ) {
        //
    }

    public function envelope(): Envelope
    {
        $subjectMap = [
            UserExtraQuestionSubmission::STATUS_APPROVED => '[THW-Trainer] Deine Zusatz-Frage wurde übernommen',
            UserExtraQuestionSubmission::STATUS_REJECTED => '[THW-Trainer] Deine Zusatz-Frage wurde abgelehnt',
            UserExtraQuestionSubmission::STATUS_CHANGED  => '[THW-Trainer] Deine Zusatz-Frage wurde mit Änderungen übernommen',
        ];

        return new Envelope(
            subject: $subjectMap[$this->submission->status] ?? '[THW-Trainer] Status deiner Zusatz-Frage',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.extra-question-submission-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
