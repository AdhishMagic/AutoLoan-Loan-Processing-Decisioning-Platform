<?php

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanReceivedForVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public LoanApplication $loan)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New loan application received for verification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan-received-for-verification',
            with: [
                'applicantName' => $this->applicantName(),
                'applicationNumber' => $this->loan->application_number,
                'officerDashboardUrl' => route('officer.review'),
            ],
        );
    }

    private function applicantName(): string
    {
        $primary = $this->loan->primaryApplicant()->first();
        $name = trim((string) ($primary?->first_name.' '.$primary?->last_name));

        if ($name !== '') {
            return $name;
        }

        return (string) ($this->loan->user?->name ?? 'Customer');
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
