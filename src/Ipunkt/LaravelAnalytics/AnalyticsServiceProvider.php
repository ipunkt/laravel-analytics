<?php

namespace Ipunkt\LaravelAnalytics;

use Config;
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
        if ($this->isLaravel4()) {
            $this->package('ipunkt/laravel-analytics');

            return;
        }

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
        $packageNamespace = $this->isLaravel4() ? 'laravel-analytics::' : '';

        $this->app->singleton('Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface', function () use ($packageNamespace) {

            //	get analytics provider name
            $provider = Config::get($packageNamespace . 'analytics.provider');

            //	make it a class
            $providerClass = 'Ipunkt\LaravelAnalytics\Providers\\' . $provider;

            //	getting the config
            $providerConfig = [];
            if (Config::has($packageNamespace . 'analytics.configurations.' . $provider)) {
                $providerConfig = Config::get($packageNamespace . 'analytics.configurations.' . $provider);
            }

            //	make provider instance
            $instance = new $providerClass($providerConfig);

            //	check if we want to prematurely disable the script block
            if (Config::get($packageNamespace . 'analytics.disable_script_block', false)) {
                $instance->disableScriptBlock();
            }

            //	return the provider instance
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

    /**
     * are we on laravel 4
     *
     * @return bool
     */
    private function isLaravel4()
    {
        return version_compare(\Illuminate\Foundation\Application::VERSION, '5', '<');
    }
}
