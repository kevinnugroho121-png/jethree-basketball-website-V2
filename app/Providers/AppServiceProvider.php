<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // [SAKTI UTAMA NGROK & PAGINATION FIX]
        // Jika mendeteksi diakses lewat terowongan Ngrok
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) || isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
            
            // PAKSA SEMUA LINK (TERMASUK PAGINATION) PAKAI HTTPS
            \URL::forceScheme('https');
            
            if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
                \URL::forceRootUrl('https://' . $_SERVER['HTTP_X_ORIGINAL_HOST']);
            } else {
                \URL::forceRootUrl('https://' . $_SERVER['HTTP_X_FORWARDED_HOST']);
            }
        }
    }
}
