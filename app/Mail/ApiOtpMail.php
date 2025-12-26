<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Your API Access OTP')
            ->view('emails.api-otp')
            ->with([
                'name' => $this->user->name,
                'code' => $this->code,
            ]);
    }
}
