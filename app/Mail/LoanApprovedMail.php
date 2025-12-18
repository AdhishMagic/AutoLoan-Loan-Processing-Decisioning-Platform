<?php

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanApprovedMail extends Mailable implements ShouldQueue
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
            subject: 'Your loan is approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan-approved',
            with: [
                'applicationNumber' => $this->loan->application_number,
                'approvedAmount' => $this->loan->sanctioned_amount ?? $this->loan->requested_amount,
                'tenureMonths' => $this->loan->sanctioned_tenure_months
                    ?? $this->loan->requested_tenure_months
                    ?? $this->loan->tenure_months,
                'interestRate' => $this->loan->sanctioned_interest_rate ?? $this->loan->requested_interest_rate,
                'loanShowUrl' => route('loans.show', $this->loan),
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
