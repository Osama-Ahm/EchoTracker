<?php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidentStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $incident;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Incident $incident, string $oldStatus, string $newStatus)
    {
        $this->incident = $incident;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Incident Status Updated - ' . $this->incident->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.incident-status-updated',
            with: [
                'incident' => $this->incident,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
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
