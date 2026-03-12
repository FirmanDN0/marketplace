<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: role:admin  or  role:admin,provider
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role, $roles, true)) {
            abort(403, 'Unauthorized.');
        }

        if ($request->user()->status !== 'active') {
            abort(403, 'Your account has been suspended.');
        }

        return $next($request);
    }
}
