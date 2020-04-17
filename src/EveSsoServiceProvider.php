<?php
/**
 * Service provider for EVESSO Socialite driver
 */
 
namespace Arcyfa\EveSso;

use Illuminate\Support\ServiceProvider;

class EveSsoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * @return void
     */
    public function register()
    {
        $this->app->make('Arcyfa\EveSso\EveSsoController');

        $this->mergeConfigFrom(
            __DIR__.'/../config/eve-sso.php', 'eve-sso'
        );
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {
        // publish configuration file. Config using .env
        $configPath = __DIR__.'/../config/eve-sso.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('eve-sso.php');
        } else {
            $publishPath = base_path('config/eve-sso.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        //$this->publishes([
        //    __DIR__.'/../database/migrations/' => database_path('migrations')
        //], 'migrations');

        // load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        // register driver eve sso to socialite
        $this->bootEsiSocialite();
    }

    private function bootEsiSocialite()
    {
        // Eve online sso driver for socialite
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'esi',
            function ($app) use ($socialite) {
                $config = config('eve-sso.esi');
                return $socialite->buildProvider(EveSsoProvider::class, $config);
            }
        );
    }

}
