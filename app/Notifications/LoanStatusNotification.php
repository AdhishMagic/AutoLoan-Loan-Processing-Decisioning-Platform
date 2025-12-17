<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public LoanApplication $loan,
        public ?string $customMessage = null
    ) {
    }

    /**
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        // Keep delivery simple and reliable by default
        return ['database'];
    }

    /**
     * Optional: If/when mail is configured, this will render a friendly email.
     * Not used unless 'mail' is added to via().
     */
    public function toMail($notifiable): MailMessage
    {
        $status = $this->loan->status;
        $url = route('loans.show', $this->loan->id);

        return (new MailMessage)
            ->subject('Loan status updated')
            ->greeting('Hello!')
            ->line($this->customMessage ?: "Your loan application status is now: {$status}.")
            ->action('View your application', $url)
            ->line('Thank you for using our platform.');
    }

    /**
     * Store a compact payload in the notifications table.
     *
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'loan_id' => $this->loan->id,
            'title' => 'Loan status updated',
            'status' => $this->loan->status,
            'amount' => $this->loan->amount ?? null,
            'message' => $this->customMessage ?: "Your loan #{$this->loan->id} is now {$this->loan->status}.",
            'link' => route('loans.show', $this->loan->id),
        ];
    }
}
