<?php namespace Ipunkt\LaravelAnalytics\Providers;

use InvalidArgumentException;
use Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface;
use Ipunkt\LaravelAnalytics\Data\Campaign;
use Ipunkt\LaravelAnalytics\Data\Event;
use Ipunkt\LaravelAnalytics\TrackingBag;
use App;

/**
 * Class GoogleAnalytics
 *
 * @package Ipunkt\LaravelAnalytics\Providers
 */
class GoogleAnalytics implements AnalyticsProviderInterface
{
	/**
	 * tracking id
	 *
	 * @var string
	 */
	private $trackingId;

	/**
	 * tracking domain
	 *
	 * @var string
	 */
	private $trackingDomain;

	/**
	 * anonymize users ip
	 *
	 * @var bool
	 */
	private $anonymizeIp = false;

	/**
	 * auto tracking the page view
	 *
	 * @var bool
	 */
	private $autoTrack = false;

	/**
	 * debug mode
	 *
	 * @var bool
	 */
	private $debug = false;

	/**
	 * for event tracking it can mark track as non-interactive so the bounce-rate calculation ignores that tracking
	 *
	 * @var bool
	 */
	private $nonInteraction = false;

	/**
	 * session tracking bag
	 *
	 * @var TrackingBag
	 */
	private $trackingBag;

	/**
	 * use https for the tracking measurement url
	 *
	 * @var bool
	 */
	private $secureTrackingUrl = true;

	/**
	 * setting options via constructor
	 *
	 * @param array $options
	 *
	 * @throws InvalidArgumentException when tracking id not set
	 */
	public function __construct(array $options = array())
	{
		$this->trackingId = array_get($options, 'tracking_id');
		$this->trackingDomain = array_get($options, 'tracking_domain', 'auto');
		$this->anonymizeIp = array_get($options, 'anonymize_ip', false);
		$this->autoTrack = array_get($options, 'auto_track', false);
		$this->debug = array_get($options, 'debug', false);

		if ($this->trackingId === null) {
			throw new InvalidArgumentException('Argument tracking_id can not be null');
		}

		$this->trackingBag = new TrackingBag;
	}

	/**
	 * track an page view
	 *
	 * @param null|string $page
	 * @param null|string $title
	 * @param null|string $hittype
	 *
	 * @return void
	 */
	public function trackPage($page = null, $title = null, $hittype = null)
	{
		$allowedHitTypes = ['pageview', 'appview', 'event', 'transaction', 'item', 'social', 'exception', 'timing'];
		if ($hittype === null) {
			$hittype = $allowedHitTypes[0];
		}

		if ( ! in_array($hittype, $allowedHitTypes)) {
			return;
		}

		$trackingCode = "ga('send', 'pageview');";

		if ($page !== null || $title !== null || $hittype !== null) {
			$page = ($page === null) ? "window.location.protocol + '//' + window.location.hostname + window.location.pathname + window.location.search" : "'{$page}'";
			$title = ($title === null) ? "document.title" : "'{$title}'";

			$trackingCode = "ga('send', {'hitType': '{$hittype}', 'page': {$page}, 'title': '{$title}'});";
		}

		$this->trackingBag->add($trackingCode);
	}

	/**
	 * track an event
	 *
	 * @param string $category
	 * @param string $action
	 * @param null|string $label
	 * @param null|int $value
	 */
	public function trackEvent($category, $action, $label = null, $value = null)
	{
		$command = '';
		if ($label !== null) {
			$command .= ", '{$label}'";
			if ($value !== null && is_numeric($value)) {
				$command .= ", {$value}";
			}
		}

		$trackingCode = "ga('send', 'event', '{$category}', '{$action}'$command);";

		$this->trackingBag->add($trackingCode);
	}

	/**
	 * track any custom code
	 *
	 * @param string $customCode
	 *
	 * @return void
	 */
	public function trackCustom($customCode)
	{
		$this->trackingBag->add($customCode);
	}

	/**
	 * enable auto tracking
	 *
	 * @return void
	 */
	public function enableAutoTracking()
	{
		$this->autoTrack = true;
	}

	/**
	 * disable auto tracking
	 *
	 * @return void
	 */
	public function disableAutoTracking()
	{
		$this->autoTrack = false;
	}

	/**
	 * returns the javascript embedding code
	 *
	 * @return string
	 */
	public function render()
	{
		$script[] = $this->_getJavascriptTemplateBlockBegin();

		if ($this->debug || App::environment('local')) {
			$script[] = "ga('create', '{$this->trackingId}', { 'cookieDomain': 'none' });";
		} else {
			$script[] = "ga('create', '{$this->trackingId}', '{$this->trackingDomain}');";
		}

		if ($this->anonymizeIp) {
			$script[] = "ga('set', 'anonymizeIp', true);";
		}

		if ($this->nonInteraction) {
			$script[] = "ga('set', 'nonInteraction', true);";
		}

		$trackingStack = $this->trackingBag->get();
		if (count($trackingStack)) {
			$script[] = implode("\n", $trackingStack);
		}

		if ($this->autoTrack) {
			$script[] = "ga('send', 'pageview');";
		}
		$script[] = $this->_getJavascriptTemplateBlockEnd();

		return implode('', $script);
	}

	/**
	 * sets or gets nonInteraction
	 *
	 * setting: $this->nonInteraction(true)->render();
	 * getting: if ($this->nonInteraction()) echo 'non-interaction set';
	 *
	 * @param boolean|null $value
	 *
	 * @return bool|$this
	 */
	public function nonInteraction($value = null)
	{
		if (null === $value) {
			return $this->nonInteraction;
		}

		$this->nonInteraction = ($value === true);

		return $this;
	}

	/**
	 * returns start block
	 *
	 * @return string
	 */
	protected function _getJavascriptTemplateBlockBegin()
	{
		$appendix = $this->debug ? '_debug' : '';

		return "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics{$appendix}.js','ga');";
	}

	/**
	 * returns end block
	 *
	 * @return string
	 */
	protected function _getJavascriptTemplateBlockEnd()
	{
		return '</script>';
	}

	/**
	 * make the tracking measurement url unsecure
	 *
	 * @return $this
	 */
	public function unsecureMeasurementUrl()
	{
		$this->secureTrackingUrl = false;

		return $this;
	}

	/**
	 * use the secured version of the tracking measurement url
	 *
	 * @return $this
	 */
	public function secureMeasurementUrl()
	{
		$this->secureTrackingUrl = false;

		return $this;
	}

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
	 *
	 * @return string
	 *
	 * @experimental
	 */
	public function trackMeasurementUrl($metricName, $metricValue, Event $event, Campaign $campaign, $clientId = null, array $params = array())
	{
		$uniqueId = ($clientId !== null) ? $clientId : uniqid('track_');

		if ($event->getLabel() === '') {
			$event->setLabel($uniqueId);
		}

		if ($campaign->getName() === '') {
			$campaign->setName('Campaign ' . date('Y-m-d'));
		}

		$protocol = $this->secureTrackingUrl ? 'https' : 'http';

		$defaults = [
			'url' => $protocol . '://www.google-analytics.com/collect?',
			'params' => [
				'v' => 1,    //	protocol version
				'tid' => $this->trackingId,    //	tracking id
				'cid' => $uniqueId,    //	client id
				't' => $event->getHitType(),
				'ec' => $event->getCategory(),
				'ea' => $event->getAction(),
				'el' => $event->getLabel(),
				'cs' => $campaign->getSource(),
				'cm' => $campaign->getMedium(),
				'cn' => $campaign->getName(),
				$metricName => $metricValue,    //	metric data
			],
		];

		$url = isset($params['url']) ? $params['url'] : $defaults['url'];
		$url = rtrim($url, '?') . '?';

		if (isset($params['url']))
			unset($params['url']);

		$params = array_merge($defaults['params'], $params);
		$queryParams = [];
		foreach ($params as $key => $value) {
			if ( ! empty($value))
				$queryParams[] = sprintf('%s=%s', $key, $value);
		}

		return $url . implode('&', $queryParams);
	}
}