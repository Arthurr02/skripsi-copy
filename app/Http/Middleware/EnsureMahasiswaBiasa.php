<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMahasiswaBiasa
{
    /** Keep committee accounts inside the committee workspace. */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isPanitia()) {
            return redirect()
                ->route('panitia.dashboard')
                ->with('error_server', 'Akun panitia hanya dapat mengakses menu kepanitiaan.');
        }

        return $next($request);
    }
}
