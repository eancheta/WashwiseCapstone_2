<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // ✅ Add this

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
        Inertia::share([
            // ✅ Flash messages
            'flash' => fn () => [
                'message' => Session::get('flash'),
            ],

            // ✅ Authenticated user (null if not logged in)
            'auth' => fn () => [
                'user' => Auth::check() ? Auth::user() : null, // 👈 use facade here
            ],
        ]);
    }
}
