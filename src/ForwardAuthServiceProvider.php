<?php

namespace FreeBuu\ForwardAuth;

use Illuminate\Support\ServiceProvider;

class ForwardAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('forward-auth.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'forward-auth');
    }
}
