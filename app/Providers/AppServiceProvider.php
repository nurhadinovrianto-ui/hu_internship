<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

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
        Paginator::useBootstrap();

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Override Google OAuth credentials dynamically from database settings
        try {
            if (class_exists(\App\Models\Setting::class) && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                config([
                    'services.google.client_id' => \App\Models\Setting::getValue('google_client_id') ?: env('GOOGLE_CLIENT_ID'),
                    'services.google.client_secret' => \App\Models\Setting::getValue('google_client_secret') ?: env('GOOGLE_CLIENT_SECRET'),
                    'services.google.redirect' => \App\Models\Setting::getValue('google_redirect_uri') ?: env('GOOGLE_REDIRECT_URI'),
                ]);
            }
        } catch (\Exception $e) {
            // Silence exceptions during early boot or migrations
        }
    }
}
