<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        // Semak jika pengguna log masuk DAN mempunyai peranan 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Jika bukan admin, halang dan hantar semula ke dashboard pelanggan
        return redirect()->route('dashboard')->with('error', 'Akses ditolak! Anda bukan Pentadbir.');
    }
}
