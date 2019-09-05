<?php

namespace Ipunkt\LaravelAnalytics;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../../config/analytics.php');

        $this->mergeConfigFrom($config, 'analytics');

        $this->publishes([
            $config => config_path('analytics.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface',
            function () {
                // get analytics provider name
                $provider = Config::get('analytics.provider');

                // make it a class
                $providerClass = 'Ipunkt\LaravelAnalytics\Providers\\' . $provider;

                // getting the config
                $providerConfig = [];
                if (Config::has('analytics.configurations.' . $provider)) {
                    $providerConfig = Config::get('analytics.configurations.' . $provider);
                }

                // make provider instance
                $instance = new $providerClass($providerConfig);

                // check if we want to prematurely disable the script block
                if (Config::get('analytics.disable_script_block', false)) {
                    $instance->disableScriptBlock();
                }

                return $instance;
            });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
