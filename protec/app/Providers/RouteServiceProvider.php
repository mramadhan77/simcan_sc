<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

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
        // Route::middleware('web')
        //      ->namespace($this->namespace)
        //      ->group(base_path('routes/web.php'));

        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
            // require base_path('app/routes/web_can.php');
            require base_path('app/routes/web_umum.php');
            require base_path('app/routes/web_parameter.php');
            require base_path('app/routes/web_ang.php');
            require base_path('app/routes/report_can.php');
            require base_path('app/routes/web_kin.php');
            require base_path('app/routes/web_limath.php');
            require base_path('app/routes/web_rkpd.php');
            require base_path('app/routes/web_renja.php');
            require base_path('app/routes/web_musren.php');
            require base_path('app/routes/web_transfer.php');
            require base_path('app/routes/web_datadasar.php');
        });  
           
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('app/routes/api.php'));
    }
}
