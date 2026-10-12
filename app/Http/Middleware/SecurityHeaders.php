<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline hardening headers. CSP deliberately omitted: the UI relies on
 * banyak handler inline (onclick, x-on) yang butuh unsafe-inline.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        // Menonaktifkan filter XSS lama yang justru rawan bypass; perlindungan
        // XSS modern datang dari escaping + CSP.
        $response->headers->set('X-XSS-Protection', '0');

        return $response;
    }
}
