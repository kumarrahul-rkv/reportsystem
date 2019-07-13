<?php

namespace Ibarts\Reportsystem;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\Controller;
    
class ReportsystemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'reportsystem');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/ibarts/reportsystem'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
       
    }
}
