<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        // Admin is allowed to access any role-protected route.
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }

        $name = $user?->role?->name;
        if (! in_array($name, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
