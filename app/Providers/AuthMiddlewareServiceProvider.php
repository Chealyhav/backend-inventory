<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AuthMiddleware;

class AuthMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the custom middleware with specific routes or globally
        $this->app['router']->aliasMiddleware('auth.jwt', AuthMiddleware::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // You can bind services in the container if needed
    }
}
