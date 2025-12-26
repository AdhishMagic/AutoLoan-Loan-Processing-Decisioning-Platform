<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoanCacheService;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __construct(private readonly LoanCacheService $cache)
    {
    }

    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $profile = $this->cache->getUserProfile((int) $user->id, function () use ($user) {
            $fresh = User::query()
                ->with('role')
                ->select(['id', 'name', 'email', 'role_id'])
                ->find($user->id);

            if (! $fresh) {
                return null;
            }

            return [
                'id' => $fresh->id,
                'name' => $fresh->name,
                'email' => $fresh->email,
                'role' => $fresh->role?->name,
            ];
        });

        if (! is_array($profile)) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json($profile);
    }
}
