<?php

namespace DiamondLGTAble\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use DiamondLGTAble\Services\Tablize;

class ViewProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('lgtable', function ($app) {
            return new Tablize();
        });
    }

    /**
     * Perform post-registration booting of services.
     * @see  https://laravel.com/docs/5.6/packages#views
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'lgtable');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }
}
