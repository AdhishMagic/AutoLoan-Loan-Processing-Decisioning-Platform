<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $name = $request->user()?->role?->name;
        if (! in_array($name, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
