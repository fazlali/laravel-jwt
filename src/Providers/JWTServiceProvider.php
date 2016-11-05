<?php

namespace Fazlali\LaravelJWT\Providers;

use Illuminate\Support\ServiceProvider;


class JWTServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('laravel-jwt.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../Models/' => app_path()
        ], 'JWT');
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('fazlali.jwt.api', function($app){
            return new \Fazlali\LaravelJWT\JWTAPI($app['request']);
        });
        $this->app->bind('fazlali.jwt.auth', function($app){
            return new \Fazlali\LaravelJWT\JWTAuth($app['request'], $app['auth']);
        });

    }

}
