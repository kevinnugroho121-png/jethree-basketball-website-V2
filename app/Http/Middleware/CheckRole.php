<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek 1: Apakah user sudah login?
        // Cek 2: Apakah role user yang login == role yang diizinkan ($role)?
        if (!$request->user() || $request->user()->role !== $role) {

            // Jika tidak sesuai, lempar dia kembali ke halaman dashboard biasa
            return redirect('/dashboard');
        }

        // Jika role-nya sesuai, izinkan dia melanjutkan ke halaman yang dituju
        return $next($request);
    }
}