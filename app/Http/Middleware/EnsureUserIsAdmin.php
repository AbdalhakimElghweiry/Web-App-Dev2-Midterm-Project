<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protects admin-only routes (users, habits, badges management, admin dashboard).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Admins only.');
        }

        return $next($request);
    }
}
