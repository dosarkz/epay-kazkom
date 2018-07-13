<?php
namespace Dosarkz\EPayKazCom;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Dosarkz\EPayKazCom\Facades\Epay as EpayFacade;

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
	    $loader = AliasLoader::getInstance();
	    $loader->alias('Crud', EpayFacade::class);

        $this->app->singleton("epay", function($app)
        {
            return new Epay();
        });

    }
}
