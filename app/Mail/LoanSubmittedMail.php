<?php

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;

class LoanSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var int
     */
    public $tries = 1;

    public function __construct(public LoanApplication $loan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your loan application',
        );
    }

    /**
     * Queue middleware for safe throttling (e.g., Mailtrap free-tier).
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            // Serialize outbound mail to avoid provider "emails per second" throttles.
            (new RateLimited('mail')),
        ];
    }

    public function content(): Content
    {
        $loan = $this->loan->loadMissing('user');

        return new Content(
            view: 'emails.loan.submitted',
            with: [
                'appName' => config('app.name'),
                'appUrl' => config('app.url'),
                'applicantName' => $this->applicantName(),
                'applicationNumber' => (string) $loan->application_number,
                'amount' => $loan->requested_amount,
                'tenureMonths' => $loan->requested_tenure_months ?? $loan->tenure_months,
                'loanShowUrl' => route('loans.show', $loan),
            ],
        );
    }

    private function applicantName(): string
    {
        $primary = null;

        if ($this->loan->relationLoaded('applicants')) {
            $primary = $this->loan->applicants
                ->firstWhere('applicant_role', 'PRIMARY');
        } else {
            $primary = $this->loan->primaryApplicant()->first();
        }

        $name = trim((string) ($primary?->first_name.' '.$primary?->last_name));

        if ($name !== '') {
            return $name;
        }

        return (string) ($this->loan->user?->name ?? 'Customer');
    }

    public function attachments(): array
    {
        return [];
    }
}
