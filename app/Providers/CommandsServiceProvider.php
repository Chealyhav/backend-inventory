<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CommandsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Console\Commands\MakeService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
