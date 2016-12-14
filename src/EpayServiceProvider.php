<?php
namespace Dosarkz\EPayKazCom;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class EpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/epay.php' => config_path('epay.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/epay.php', 'epay'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['epay'] = $this->app->share(function($app)
        {
            return new Epay();
        });

    }
}
