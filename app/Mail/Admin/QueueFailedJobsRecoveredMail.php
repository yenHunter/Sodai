<?php

namespace App\Mail\Admin;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class QueueFailedJobsRecoveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $duration;

    public function __construct(
        public int $previousCount,
        public ?Carbon $startedAt,
        public Carbon $recoveredAt,
    ) {
        $this->duration = $startedAt
            ? $startedAt->diffForHumans($recoveredAt, [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'parts' => 2,
                'join' => true,
            ])
            : 'Unknown';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Queue recovered: failed-jobs backlog is clear - '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.queue-failed-jobs-recovered',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
