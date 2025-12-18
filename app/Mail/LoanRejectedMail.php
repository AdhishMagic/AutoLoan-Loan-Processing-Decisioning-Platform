<?php

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanRejectedMail extends Mailable implements ShouldQueue
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
            subject: 'Update on your loan application',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $loan = $this->loan->loadMissing('user');

        return new Content(
            view: 'emails.loan.rejected',
            with: [
                'appName' => config('app.name'),
                'appUrl' => config('app.url'),
                'applicationNumber' => (string) $loan->application_number,
                'amount' => $loan->requested_amount,
                'tenureMonths' => $loan->requested_tenure_months ?? $loan->tenure_months,
                'loanShowUrl' => route('loans.show', $loan),
            ],
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
