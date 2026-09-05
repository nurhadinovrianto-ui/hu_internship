<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                // Timezone
                $timezone = Setting::get('timezone');
                if ($timezone) {
                    Config::set('app.timezone', $timezone);
                    date_default_timezone_set($timezone);
                }

                // SMTP Mail Configuration
                $smtpHost = Setting::get('smtp_host');
                if ($smtpHost) {
                    Config::set('mail.mailers.smtp.host', $smtpHost);
                    Config::set('mail.mailers.smtp.port', Setting::get('smtp_port', 587));
                    Config::set('mail.mailers.smtp.username', Setting::get('smtp_username'));
                    Config::set('mail.mailers.smtp.password', Setting::get('smtp_password'));
                    Config::set('mail.mailers.smtp.encryption', Setting::get('smtp_encryption', 'tls'));
                    
                    Config::set('mail.from.address', Setting::get('mail_from_address', 'hello@example.com'));
                    Config::set('mail.from.name', Setting::get('mail_from_name', Setting::get('app_name', 'INTERNSHIP MANAJEMEN SISTEM')));
                }

                // Google OAuth Configuration
                $googleClientId = Setting::get('google_client_id');
                if ($googleClientId) {
                    Config::set('services.google.client_id', $googleClientId);
                    Config::set('services.google.client_secret', Setting::get('google_client_secret'));
                    Config::set('services.google.redirect', Setting::get('google_redirect_uri', url('/auth/google/callback')));
                }
            }
        } catch (\Exception $e) {
            // Jika database belum terkoneksi atau terjadi error, abaikan (bisa terjadi saat awal migrasi)
        }
    }
}
