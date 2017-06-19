# Analytics tracking package for Laravel

[![Latest Stable Version](https://poser.pugx.org/ipunkt/laravel-analytics/v/stable.svg)](https://packagist.org/packages/ipunkt/laravel-analytics) [![Latest Unstable Version](https://poser.pugx.org/ipunkt/laravel-analytics/v/unstable.svg)](https://packagist.org/packages/ipunkt/laravel-analytics) [![License](https://poser.pugx.org/ipunkt/laravel-analytics/license.svg)](https://packagist.org/packages/ipunkt/laravel-analytics) [![Total Downloads](https://poser.pugx.org/ipunkt/laravel-analytics/downloads.svg)](https://packagist.org/packages/ipunkt/laravel-analytics)

## Quickstart

```
composer require ipunkt/laravel-analytics
```

Add to `providers` in `config/app.php`:

```
Ipunkt\LaravelAnalytics\AnalyticsServiceProvider::class,
```

Add to `aliases` in `config/app.php`:

```
'Analytics' => Ipunkt\LaravelAnalytics\AnalyticsFacade::class,
```

To your `.env` add these variables and set them to your liking:

```
ANALYTICS_PROVIDER=GoogleAnalytics
ANALYTICS_TRACKING_ID=your-tracking-id
```
We support the whole configuration as  environment variables. Please see Configuration section.

Finally, just above your `</head>` closing tag place, this code:

```
{!! Analytics::render() !!}
```

**You now have Google Analytics working. Enjoy!**


## Installation

Add to your composer.json following lines

	"require": {
		"ipunkt/laravel-analytics": "~1.0"
	}

Add `Ipunkt\LaravelAnalytics\AnalyticsServiceProvider::class,` to `providers` in `app/config/app.php`.

Optional: Add `'Analytics' => Ipunkt\LaravelAnalytics\AnalyticsFacade::class,` to `aliases` in `app/config/app.php`.

Run `php artisan vendor:publish --provider="Ipunkt\LaravelAnalytics\AnalyticsServiceProvider"`

Then edit `analytics.php` in `config` to your needs. We do config merge in the service provider, so your local settings 
 will stay the same.

## Configuration

<dl>
<dt>provider</dt><dd>Provider to use, possible Providers are: <code>GoogleAnalytics</code>, <code>NoAnalytics</code></dd>
</dl>

### Google Analytics

<dl>
<dt>tracking_id</dt><dd>Tracking ID, Your tracking id for Google Analytics</dd>
<dt>tracking_domain</dt><dd>Tracking domain, unset or set to "<code>auto</code>" for automatic fallback</dd>
<dt>tracker_name</dt><dd>Tracker name</dd>
<dt>display_features</dt><dd>enabling the display features plugin, possible values: <code>(true|false)</code></dd>
<dt>anonymize_ip</dt><dd>anonymize users ip, possible values: <code>(true|false)</code></dd>
<dt>auto_track</dt><dd>auto tracking current pageview, possible values: <code>(true|false)</code></dd>
<dt>debug</dt><dd>enabling the debug mode, possible values: <code>(true|false)</code></dd>
</dl>

### Environment-based Configuration

You can configure the whole settings by changing/setting environment variables in your .env file. (Or .env.dusk file if you use Laravel Dusk).

Here is the mapping of configuration value and the environment-based names:
<dl>
<dt>tracking_id</dt><dd>ANALYTICS_TRACKING_ID</dd>
<dt>tracking_domain</dt><dd>ANALYTICS_TRACKING_DOMAIN (auto)</dd>
<dt>tracker_name</dt><dd>ANALYTICS_TRACKER_NAME (t0)</dd>
<dt>display_features</dt><dd>ANALYTICS_DISPLAY_FEATURES (false)</dd>
<dt>anonymize_ip</dt><dd>ANALYTICS_ANONYMIZE_IP (true)</dd>
<dt>auto_track</dt><dd>ANALYTICS_AUTO_TRACK (true)</dd>
<dt>debug</dt><dd>ANALYTICS_DEBUG (true on local environment)</dd>
</dl>

This behaviour was integrated with version 1.3.2.

### Other

<dl>
<dt>disable_script_block</dt><dd>You can disable script block generation by configuration value, possible values: <code>(true|false)</code>, false by default.</dd>
</dl>

## Usage

In controller action (or anywhere else) use following statement to track an event or page view:

	//	tracking the current page view
	Analytics::trackPage();	// only necessary if `auto_track` is false or Analytics::disableAutoTracking() was called before

	//	tracking an event
	Analytics::trackEvent('category', 'action');

	//	tracking a custom line
	Analytics::trackCustom("ga('send', ......"); // this line will be added within the tracking script

You can set an optional campaign for the tracking:

	// creating a campaign
	$campaign = new \Ipunkt\LaravelAnalytics\Data\Campaign('Sales2016');
	$campaign->setMedium('web')->setSource('IT Magazine')->setKeyword('Hot stuff');
	Analytics::setCampaign($campaign);

You can set an user id for the tracking:

	// creating a campaign
	Analytics::setUserId($userIdentificationHash);

In your view or layout template (e.g. a blade template) use the following statement:

	{!! Analytics::render() !!}

For Google Analytics you should place the statement right below the `body` tag

	<body>{!! Analytics::render() !!}

### Dependency Injection (since 1.2.0)

You can inject the analytics provider by referencing the interface:

	class PageController extends Controller
	{
		public function show(\Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface $analytics)
		{
			$analytics->setUserId(md5(\Auth::user()->id)); // identical to Analytics::setUserId(md5(\Auth::user()->id));
			return view('welcome');
		}
	}

## How to use

The `GoogleAnalytics` Provider automatically controls the [local environment](https://developers.google.com/analytics/devguides/collection/analyticsjs/advanced#localhost) behaviour for testing purposes.

There is a builtin provider called `NoAnalytics`. This is for testing environments and tracks nothing. So you do
not have to rewrite your code, simple select this `provider` in `analytics` configuration for your special environment
configurations.

### Track a measurement without having javascript

1. Log in to Google Analytics and create custom definition. There you create a custom metrics.
   For example: Email opens, Integer type, min: 0 and max: 1
   This will be available as `metric1`.

2. Within your mail template (or page template) you have to create a tracking image

	`<img src="{!! Analytics::trackMeasurementUrl('metric1', '1', new Event, new Campaign, md5($user)) !!}" width="1" height="1" style="background-color: transparent; border: 0 none;" />`

3. That's it

## Content Security Policy

Since version 1.3 is a support for Content Security Policy integrated.

Please call the `withCSP()` on the provider interface instance.

Then use an additional package (or implement on your own) `martijnc/php-csp` and use the following code in your middleware or your javascript sending controller:
```php
use Phpcsp\Security\ContentSecurityPolicyHeaderBuilder;

$policy = new ContentSecurityPolicyHeaderBuilder();

// Set the default-src directive to 'none'
$policy->addSourceExpression(ContentSecurityPolicyHeaderBuilder::DIRECTIVE_DEFAULT_SRC, 'none');

// Add the nonce to the script-src directive
$policy->addNonce(ContentSecurityPolicyHeaderBuilder::DIRECTIVE_SCRIPT_SRC, $analytics->withCSP()->cspNonce());

foreach ($policy->getHeaders(true) as $header) {
    header(sprintf('%s: %s', $header['name'], $header['value']));
}
```

The result looks like this:
```php
array (size=1)
  0 => 
    array (size=2)
      'name' => string 'Content-Security-Policy' (length=23)
      'value' => string 'default-src 'none'; script-src 'nonce-RandomNonceStringFromAnalyticsProvider';' (length=58)
```

On rendering your analytics script the `nonce` attribute will be automatically added on your script tag.

## API Documentation

For the correct usage methods look at the `Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface.php`

### Analytics::render()

Context: Blade Templates, View

For rendering the correct javascript code. It is necessary to have it in all layout files to track your actions and page calls.

	/**
	 * returns the javascript code for embedding the analytics stuff
	 *
	 * @return string
	 */
	public function render();


### Analytics::trackPage()

Context: Controller, Action code

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

Context: Controller, Action code

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

Context: Controller, Action code

For tracking a custom script line within the embedded analytics code.

	/**
	 * track any custom code
	 *
	 * @param string $customCode
	 * @return void
	 */
	public function trackCustom($customCode);


### Analytics::enableDisplayFeatures()

Context: Controller, Action code

Enabling the auto tracking, overriding the configuration setting `auto_track`.

	/**
	 * enable display features
	 *
	 * @return GoogleAnalytics
	 */
	public function enableAutoTracking();


### Analytics::disableDisplayFeatures()

Context: Controller, Action code

Disabling the auto tracking, overriding the configuration setting `auto_track`.

	/**
	 * disable display features
	 *
	 * @return GoogleAnalytics
	 */
	public function disableAutoTracking();

### Analytics::enableAutoTracking()

Context: Controller, Action code

Enabling the auto tracking, overriding the configuration setting `auto_track`.

	/**
	 * enable auto tracking
	 *
	 * @return GoogleAnalytics
	 */
	public function enableAutoTracking();


### Analytics::disableAutoTracking()

Context: Controller, Action code

Disabling the auto tracking, overriding the configuration setting `auto_track`.

	/**
	 * disable auto tracking
	 *
	 * @return GoogleAnalytics
	 */
	public function disableAutoTracking();

### Analytics::trackMeasurementUrl()

Context: Blade Template, View

Sometimes you have to track measurements, e.g. opening an email newsletter. There you have no javascript at all.

	/**
	 * assembles an url for tracking measurement without javascript
	 *
	 * e.g. for tracking email open events within a newsletter
	 *
	 * @param string $metricName
	 * @param mixed $metricValue
	 * @param \Ipunkt\LaravelAnalytics\Data\Event $event
	 * @param \Ipunkt\LaravelAnalytics\Data\Campaign $campaign
	 * @param string|null $clientId
	 * @param array $params
	 * @return string
	 */
	public function trackMeasurementUrl($metricName, $metricValue, Event $event, Campaign $campaign, $clientId = null, array $params = array());

### Analytics::setUserId($userId)

Context: Controller, Action code

Adding an user id to analytics tracking. This user id is a user-dependent unique id. But be careful, you should have no
 direct relation to the special user itself - it should be unique per user for analyzing.

This user tracking is implemented at [Google Analytics](https://developers.google.com/analytics/devguides/collection/analyticsjs/cookies-user-id).

	/**
	 * sets an user id for user tracking
	 *
	 * @param string $userId
	 * @return AnalyticsProviderInterface
	 */
	public function setUserId($userId);

Available since 1.1.4.

### Analytics::unsetUserId()

Context: Controller, Action code

Removing of an user id is also possible.

	/**
	 * unset an user id
	 *
	 * @return AnalyticsProviderInterface
	 */
	public function unsetUserId(); 

Available since 1.1.4.

### Analytics::setCampaign($campaign)

Context: Controller, Action code

Adding a campaign to the current tracking.

This campaign tracking is documented for [Google Analytics](https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#campaignName).

    /**
     * sets a campaign
     *
     * @param Campaign $campaign
     * @return AnalyticsProviderInterface
     */
    public function setCampaign(Campaign $campaign);

Available since 1.2.0.

### Analytics::unsetCampaign()

Context: Controller, Action code

Removing of a campaign is also possible.

    /**
     * unset a possible given campaign
     *
     * @return AnalyticsProviderInterface
     */
    public function unsetCampaign();

Available since 1.2.0.

### Analytics::enableScriptBlock()

Context: Controller, Action code

Enabling the rendering of the `<script>...</script>` block tags. Is enabled by default, so you do not have to call this.

	/**
	 * render script block
	 *
	 * @return GoogleAnalytics
	 */
	public function enableScriptBlock();

Available since 1.2.1.

### Analytics::disableScriptBlock()

Context: Controller, Action code

Disabling the rendering of the `<script>...</script>` block tags.

	/**
	 * do not render script block
	 *
	 * @return GoogleAnalytics
	 */
	public function disableScriptBlock();

Available since 1.2.1.

### Analytics::enableEcommerceTracking()

Context: Controller, Action code

Enabling ecommerce tracking.

    /**
     * enable ecommerce tracking
     *
     * @return AnalyticsProviderInterface
     */
    public function enableEcommerceTracking();

Available since 1.2.2.

### Analytics::disableEcommerceTracking()

Context: Controller, Action code

Disabling ecommerce tracking.

    /**
     * disable ecommerce tracking
     *
     * @return AnalyticsProviderInterface
     */
    public function disableEcommerceTracking();

Available since 1.2.2.

### Analytics::ecommerceAddTransaction()

Context: Controller, Action code

Add ecommerce transaction to tracking code.

    /**
     * ecommerce tracking - add transaction
     *
     * @param string $id
     * @param null|string $affiliation
     * @param null|float $revenue
     * @param null|float $shipping
     * @param null|float $tax
     * @param null|string $currency
     *
     * @return AnalyticsProviderInterface
     */
    public function ecommerceAddTransaction($id, $affiliation = null, $revenue = null, $shipping = null, $tax = null, $currency = null);

Available since 1.2.2. Parameter `$currency` since 1.3.3.

Since version 1.3.3 the [multi currency](https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce#multicurrency) tracking is supported with currency values defined [here](https://support.google.com/analytics/answer/6205902#supported-currencies).

### Analytics::ecommerceAddItem()

Context: Controller, Action code

Add ecommerce item to tracking code.

    /**
     * ecommerce tracking - add item
     *
     * @param string $id
     * @param string $name
     * @param null|string $sku
     * @param null|string $category
     * @param null|float $price
     * @param null|int $quantity
     * @param null|string $currency
     *
     * @return AnalyticsProviderInterface
     */
    public function ecommerceAddItem($id, $name, $sku = null, $category = null, $price = null, $quantity = null, $currency = null);

Available since 1.2.2. Parameter `$currency` since 1.3.3.

Since version 1.3.3 the [multi currency](https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce#multicurrency) tracking is supported with currency values defined [here](https://support.google.com/analytics/answer/6205902#supported-currencies).

### Analytics::setCustom()

Context: Controller, Action code

Adds custom settings.

    /**
     * sets custom dimensions
     *
     * @param string|array $dimension
     * @param null|string $value
     * @return AnalyticsProviderInterface
     */
    public function setCustom($dimension, $value = null)

Available since 1.2.2.

### Analytics::withCSP()

Context: Controller, Action code

Enabling the Content Security Policy feature.

    /**
     * enables Content Security Polity and sets nonce
     *
     * @return AnalyticsProviderInterface
     */
    public function withCSP();

Available since 1.3.0.

### Analytics::withoutCSP()

Context: Controller, Action code

Disabling the Content Security Policy feature.

    /**
     * disables Content Security Polity
     *
     * @return AnalyticsProviderInterface
     */
    public function withoutCSP();

Available since 1.3.0.

### Analytics::cspNonce()

Context: Controller, Action code

Returns the nonce generated for the Content Security Policy Header.

    /**
     * returns the current Content Security Policy nonce
     *
     * @return string|null
     */
    public function cspNonce();

Available since 1.3.0.
