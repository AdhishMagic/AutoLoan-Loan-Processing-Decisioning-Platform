<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->api_otp_verified_at) {
            return response()->json([
                'message' => 'OTP verification required. Please verify your email OTP to access the API.'
            ], 403);
        }

        return $next($request);
    }
}
