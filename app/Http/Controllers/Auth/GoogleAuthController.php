<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OauthAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth failed', ['error' => $e->getMessage()]);
            return redirect()->route('login')->withErrors(['email' => 'Google authentication failed.']);
        }

        $user = DB::transaction(function () use ($googleUser) {
            $oauth = OauthAccount::query()
                ->where('provider', 'google')
                ->where('provider_user_id', $googleUser->getId())
                ->first();

            if ($oauth) {
                $user = $oauth->user;
                // Update email/name if changed
                $user->forceFill([
                    'name' => $googleUser->getName() ?: $user->name,
                    'email' => $googleUser->getEmail() ?: $user->email,
                    'email_verified_at' => now(),
                ])->save();
                return $user;
            }

            // Find existing user by email, or create a new one
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Google User'),
                    'password' => null,
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'role_id' => function () {
                        return (int) (Role::query()->where('name', 'user')->value('id')
                            ?: DB::table('roles')->insertGetId([
                                'name' => 'user',
                                'description' => 'Loan applicant',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]));
                    },
                ]
            );

            // Attach oauth account
            OauthAccount::updateOrCreate(
                [
                    'provider' => 'google',
                    'provider_user_id' => $googleUser->getId(),
                ],
                [
                    'user_id' => $user->id,
                ]
            );

            return $user;
        });

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
