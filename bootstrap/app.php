<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. DAFTARKAN ALIAS ROLE DI SINI (PENTING!)
        // Ini agar Laravel tahu bahwa 'role' merujuk ke file CheckRole kamu
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. PENGECUALIAN CSRF UNTUK MIDTRANS
        $middleware->validateCsrfTokens(except: [
            'api/payment/callback', // Izinkan jalur ini diakses tanpa token CSRF
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();