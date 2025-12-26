<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tokens = $user->tokens()->latest()->get(['id', 'name', 'abilities', 'last_used_at', 'created_at']);

        return view('api-keys.index', [
            'tokens' => $tokens,
            'newToken' => session('plainTextToken'),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user->api_otp_verified_at) {
            return redirect()->route('api-keys.index')->with('status', 'Please verify OTP before creating API keys.');
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:64'],
        ]);

        $plainTextToken = $user->createToken($validated['name'])->plainTextToken;

        return redirect()->route('api-keys.index')->with('plainTextToken', $plainTextToken);
    }

    public function destroy(Request $request, string $tokenId)
    {
        $user = $request->user();
        $user->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('api-keys.index')->with('status', 'API key revoked');
    }
}
