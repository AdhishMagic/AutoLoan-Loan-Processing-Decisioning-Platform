<?php

namespace Tests\Feature;

use App\Events\LoanApproved;
use App\Events\LoanRejected;
use App\Events\LoanSubmitted;
use App\Listeners\SendLoanApprovedEmail;
use App\Listeners\SendLoanRejectedEmail;
use App\Listeners\SendLoanSubmittedEmail;
use App\Mail\LoanApprovedMail;
use App\Mail\LoanRejectedMail;
use App\Mail\LoanSubmittedMail;
use App\Models\Applicant;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoanLifecycleEmailsTest extends TestCase
{
    public function test_it_queues_submitted_email_when_event_is_dispatched(): void
    {
        Mail::fake();

        $user = new User([
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
        ]);
        $user->id = 1;
        $user->exists = true;

        $loan = new LoanApplication([
            'user_id' => 1,
            'status' => 'SUBMITTED',
            'application_number' => 'LAP-TEST-0001',
            'requested_amount' => 250000,
            'requested_tenure_months' => 60,
            'stage_order' => 8,
        ]);
        $loan->id = '00000000-0000-0000-0000-000000000001';
        $loan->exists = true;
        $loan->setRelation('user', $user);

        $primaryApplicant = new Applicant([
            'applicant_role' => 'PRIMARY',
            'first_name' => 'Test',
            'last_name' => 'Applicant',
        ]);
        $loan->setRelation('applicants', collect([$primaryApplicant]));

        (new SendLoanSubmittedEmail())->handle(new LoanSubmitted($loan));

        Mail::assertQueued(LoanSubmittedMail::class, function (LoanSubmittedMail $mailable) use ($loan) {
            return $mailable->loan->application_number === $loan->application_number;
        });
    }

    public function test_it_queues_approved_email_when_event_is_dispatched(): void
    {
        Mail::fake();

        $user = new User([
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
        ]);
        $user->id = 1;
        $user->exists = true;

        $loan = new LoanApplication([
            'user_id' => 1,
            'status' => 'APPROVED',
            'application_number' => 'LAP-TEST-0002',
            'requested_amount' => 250000,
            'requested_tenure_months' => 60,
            'stage_order' => 8,
        ]);
        $loan->id = '00000000-0000-0000-0000-000000000002';
        $loan->exists = true;
        $loan->setRelation('user', $user);

        (new SendLoanApprovedEmail())->handle(new LoanApproved($loan));

        Mail::assertQueued(LoanApprovedMail::class, function (LoanApprovedMail $mailable) use ($loan) {
            return $mailable->loan->application_number === $loan->application_number;
        });
    }

    public function test_it_queues_rejected_email_when_event_is_dispatched(): void
    {
        Mail::fake();

        $user = new User([
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
        ]);
        $user->id = 1;
        $user->exists = true;

        $loan = new LoanApplication([
            'user_id' => 1,
            'status' => 'REJECTED',
            'application_number' => 'LAP-TEST-0003',
            'requested_amount' => 250000,
            'requested_tenure_months' => 60,
            'stage_order' => 8,
        ]);
        $loan->id = '00000000-0000-0000-0000-000000000003';
        $loan->exists = true;
        $loan->setRelation('user', $user);

        (new SendLoanRejectedEmail())->handle(new LoanRejected($loan));

        Mail::assertQueued(LoanRejectedMail::class, function (LoanRejectedMail $mailable) use ($loan) {
            return $mailable->loan->application_number === $loan->application_number;
        });
    }
}
