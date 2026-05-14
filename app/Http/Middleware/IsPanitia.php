<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPanitia
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAnggota()) {
            return $next($request); // Silakan lewat
        }

        // Jika bukan panitia tapi maksa masuk, tampilkan halaman 403 Forbidden
        abort(403, 'Akses Ditolak. Anda bukan Panitia Rekrutmen.');
    }
}
