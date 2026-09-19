<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Izinkan akses hanya untuk user dengan role user (masyarakat/relawan).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isUser()) {
            abort(403, 'Akses ini hanya untuk kontributor (user) AksesKita.');
        }

        return $next($request);
    }
}
