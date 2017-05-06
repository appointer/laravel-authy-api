<?php

namespace Appointer\AuthyApi;

use Illuminate\Support\ServiceProvider;

class AuthyApiServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/authy-api.php' => config_path('authy-api.php'),
        ], 'public');
    }

    public function register()
    {
        $this->app->singleton(AuthyClient::class, function () {
            return new AuthyClient(
                config('authy-api.api-base-uri'),
                config('authy-api.api-key')
            );
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/authy-api.php', 'authy-api');
    }

    public function provides()
    {
        return [AuthyClient::class];
    }
}
