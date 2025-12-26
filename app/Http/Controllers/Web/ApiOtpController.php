<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ApiOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApiOtpController extends Controller
{
    public function send(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['nullable', 'string', 'max:64'],
        ]);

        $code = (string) random_int(100000, 999999);
        $user->forceFill([
            'api_otp_code_hash' => hash('sha256', $code),
            'api_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->queue(new ApiOtpMail($user, $code));

        return redirect()->route('api-keys.index')->with('status', 'OTP sent to your email.');
    }

    public function verify(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $expected = $user->api_otp_code_hash;
        $expiresAt = $user->api_otp_expires_at;

        if (! $expected || ! $expiresAt || now()->greaterThan($expiresAt)) {
            return redirect()->route('api-keys.index')->with('status', 'OTP expired or not requested.');
        }

        if (! hash_equals($expected, hash('sha256', $validated['otp']))) {
            return redirect()->route('api-keys.index')->with('status', 'Invalid OTP.');
        }

        $user->forceFill([
            'api_otp_verified_at' => now(),
            'api_otp_code_hash' => null,
            'api_otp_expires_at' => null,
        ])->save();

        return redirect()->route('api-keys.index')->with('status', 'OTP verified. You can create and use API keys.');
    }
}
