<?php

namespace FreeBuu\ForwardAuth;

use FreeBuu\ForwardAuth\Auth\AuthentikGuard;
use FreeBuu\ForwardAuth\Auth\AuthentikUserProvider;
use Illuminate\Support\Facades\Auth;
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
        $this->app['auth']->extend(config('forward-auth.guard-name'), function ($app, string $name, array $config) {
            $guard = new AuthentikGuard(
                $app['request'],
                $config['header-prefix'] ?? $app['config']['forward-auth.header-prefix'],
                $app['auth']->createUserProvider($config['provider']),
            );
            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'forward-auth');

        Auth::provider('eloquent_authentik', function ($app, $config) {
            return new AuthentikUserProvider(
                $config['identifier-name'] ?? $app['config']['forward-auth.defaults.identifier-name'],
                $config['mapper'] ?? $app['config']['forward-auth.defaults.mapper'],
                $config['validation'] ?? $app['config']['forward-auth.defaults.validation'],
                $config['create-users'] ?? $app['config']['forward-auth.defaults.create-users'],
                $config['model'] ?? null
            );
        });
    }
}
