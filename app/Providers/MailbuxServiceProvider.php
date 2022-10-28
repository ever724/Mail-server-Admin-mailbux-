<?php

namespace App\Providers;

use App\Models\SystemSetting;
use App\Services\MailbuxService;
use Illuminate\Support\ServiceProvider;

class MailbuxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $baseUrl = SystemSetting::getSetting('mailbux_domain_url');
        $username = SystemSetting::getSetting('mailbux_username');
        $password = SystemSetting::getSetting('mailbux_password');

        $this->app->bind(MailbuxService::class, function () use ($username, $password, $baseUrl) {
            return new MailbuxService(
                $username, $password, $baseUrl
            );
        });
    }
}
