<?php

namespace Dws\Importify;

use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Dws\Importify\ImportController');
        $this->loadViewsFrom(__DIR__.'/views', 'importify');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/importify'),
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes/web.php';
    }
}
