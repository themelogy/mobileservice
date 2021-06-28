<?php

namespace Themelogy\MobileService;

use Illuminate\Support\ServiceProvider;

class MobileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/mobile-service.php' => config_path('mobile-service.php'),
            ]);
        }
    }

    public function register()
    {
        $this->app->singleton(MobileService::class, function($app){
            return new MobileService();
        });
    }
}