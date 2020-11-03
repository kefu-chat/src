<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapDashboardRoutes();
        $this->mapVisitorRoutes();
        $this->mapChatRoutes();
        $this->mapBroadcastingRoutes();
        $this->mapRocketRoutes();

        // $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapDashboardRoutes()
    {
        Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
            ->group(base_path('routes/dashboard.php'));
    }

    /**
     * Define the "visitor" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapVisitorRoutes()
    {
        Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
            ->group(base_path('routes/visitor.php'));
    }

    /**
     * Define the "chat" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapChatRoutes()
    {
        Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
            ->group(base_path('routes/chat.php'));
    }

    /**
     * Define the "chat" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapRocketRoutes()
    {
        Route::middleware('api')
        ->namespace($this->namespace . '\\Rocket')
            ->group(base_path('routes/rocket.php'));
    }

    /**
     * Define the "broadcasting" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapBroadcastingRoutes()
    {
        Route::prefix('broadcasting')
        ->middleware('api')
        ->namespace($this->namespace)
            ->group(base_path('routes/broadcasting.php'));
    }
}
