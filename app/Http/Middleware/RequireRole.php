<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles, true)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'You do not have permission to access this resource.'], 403)
                : abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
