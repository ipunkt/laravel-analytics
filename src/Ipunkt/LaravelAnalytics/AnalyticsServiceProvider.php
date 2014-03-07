<?php namespace Ipunkt\LaravelAnalytics;

use Config;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider {

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
		$this->package('ipunkt/laravel-analytics');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('analytics', function () {

			//	get analytics provider name
			$provider = Config::get('ipunkt/laravel-analytics::analytics.provider');

			//	make it a class
			$providerClass = 'Ipunkt\LaravelAnalytics\Providers\\' . $provider;

			//	getting the config
			$providerConfig = [];
			if (Config::has('analytics.configurations.' . $provider))
			{
				$providerConfig = Config::get('analytics.configurations.' . $provider);
			}

			//	return an instance
			return new $providerClass($providerConfig);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}