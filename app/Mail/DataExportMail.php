<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DataExportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array{name: string, content: string}> $csvFiles
     * @param array<string, int> $summary
     */
    public function __construct(
        public User $user,
        public array $csvFiles,
        public array $summary
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dein THW-Trainer Datenexport (Art. 20 DSGVO)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.data-export',
            with: [
                'user' => $this->user,
                'summary' => $this->summary,
                'requestedAt' => now()->format('d.m.Y H:i'),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return array_map(function (array $file) {
            return Attachment::fromData(fn () => $file['content'], $file['name'])
                ->withMime('text/csv');
        }, $this->csvFiles);
    }
}
