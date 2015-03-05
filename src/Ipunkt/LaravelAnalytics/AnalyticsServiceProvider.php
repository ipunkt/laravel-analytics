<?php namespace Ipunkt\LaravelAnalytics;

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
		$this->app->bind('analytics', function () {

			//	get analytics provider name
			$provider = Config::get('laravel-analytics::analytics.provider');

			//	make it a class
			$providerClass = 'Ipunkt\LaravelAnalytics\Providers\\' . $provider;

			//	getting the config
			$providerConfig = [];
			if (Config::has('laravel-analytics::analytics.configurations.' . $provider)) {
				$providerConfig = Config::get('laravel-analytics::analytics.configurations.' . $provider);
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