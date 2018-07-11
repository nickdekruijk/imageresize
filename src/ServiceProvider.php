<?php

namespace NickDeKruijk\ImageResize;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('imageresize.php'),
        ], 'config');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        if ($this->app->runningInConsole()) {
            $this->commands([
                UserCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'imageresize');
    }
}
