<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the personal habit area separate from admin tools.
 */
class EnsureUserIsNormalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isUser()) {
            abort(Response::HTTP_FORBIDDEN, 'This area is for normal user accounts.');
        }

        return $next($request);
    }
}
