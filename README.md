# Analytics tracking package for Laravel 4.x

## Installation

Add to your composer.json following lines

	"require": {
		"ipunkt/laravel-analytics": "~1.0"
	}

Run `php artisan config:publish ipunkt/laravel-analytics`

Then edit `analytics.php` in `app/config/packages/ipunkt/laravel-analytics` to your needs.

Add `'Ipunkt\LaravelAnalytics\AnalyticsServiceProvider',` to `providers` in `app/config/app.php`.

Add `'Analytics' => 'Ipunkt\LaravelAnalytics\AnalyticsFacade',` to `aliases` in `app/config/app.php`.


## Configuration

	provider	- Provider to use, possible Providers are: GoogleAnalytics, NoAnalytics

### Google Analytics

	tracking_id	- Tracking ID

	tracking_domain	- Tracking domain, unset or set to "auto" for automatic fallback

	anonymize_ip - (true|false) anonymize users ip

	auto_track - (true|false) auto tracking current pageview

## Usage

In controller action (or anywhere else) use following statement to track an event or page view:

	//	tracking the current page view
	Analytics::trackPage();	// only necessary if `auto_track` is false or Analytics::disableAutoTracking() was called before

	//	tracking an event
	Analytics::trackEvent('category', 'action');

	//	tracking a custom line
	Analytics::trackCustom("ga('send', ......"); // this line will be added within the tracking script

In your view or layout template (e.g. a blade template) use the following statement:

	{{ Analytics::render() }}

For Google Analytics you should place the statement right behind the `body` tag

	<body>{{ Analytics::render() }}

## How to use

The `GoogleAnalytics` Provider automatically controls the local environment behaviour for testing purposes.
See https://developers.google.com/analytics/devguides/collection/analyticsjs/advanced#localhost for details.

There is a builtin provider called `NoAnalytics`. This is for testing environments and tracks nothing. So you do
not have to rewrite your code, simple select this `provider` in `analytics` configuration for your special environment
configurations.


## API Documentation

For the correct usage methods look at the `Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface.php`

### Analytics::render()

For rendering the correct javascript code.

	/**
	 * returns the javascript code for embedding the analytics stuff
	 *
	 * @return string
	 */
	public function render();


### Analytics::trackPage()

For tracking a page view.

	/**
	 * track an page view
	 *
	 * @param null|string $page
	 * @param null|string $title
	 * @param null|string $hittype
	 * @return void
	 */
	public function trackPage($page, $title, $hittype);


### Analytics::trackEvent()

For tracking an event

	/**
	 * track an event
	 *
	 * @param string $category
	 * @param string $action
	 * @param null|string $label
	 * @param null|int $value
	 * @return void
	 */
	public function trackEvent($category, $action, $label, $value);


### Analytics::trackCustom()

For tracking a custom script line within the embedded analytics code.

	/**
	 * track any custom code
	 *
	 * @param string $customCode
	 * @return void
	 */
	public function trackCustom($customCode);


### Analytics::enableAutoTracking()

Enabling the auto tracking, overriding the configuration setting `auto_track`.

	/**
	 * enable auto tracking
	 *
	 * @return void
	 */
	public function enableAutoTracking();


### Analytics::disableAutoTracking()

Disabling the auto tracking, overriding the configuration setting `auto_track`.

	/**
	 * disable auto tracking
	 *
	 * @return void
	 */
	public function disableAutoTracking();

