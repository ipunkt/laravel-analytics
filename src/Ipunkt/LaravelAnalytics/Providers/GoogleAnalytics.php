<?php

namespace Ipunkt\LaravelAnalytics\Providers;

use App;
use InvalidArgumentException;
use Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface;
use Ipunkt\LaravelAnalytics\Data\Campaign;
use Ipunkt\LaravelAnalytics\Data\Event;
use Ipunkt\LaravelAnalytics\Data\Renderer\CampaignRenderer;
use Ipunkt\LaravelAnalytics\TrackingBag;

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
     * tracker name
     *
     * @var string
     */
    private $trackerName;

    /**
     * display features plugin enabled or disabled
     *
     * @var bool
     */
    private $displayFeatures = false;

    /**
     * ecommerce tracking plugin enabled or disabled
     *
     * @var bool
     */
    private $ecommerceTracking = false;

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
     * a user id for tracking
     *
     * @var string|null
     */
    private $userId = null;

    /**
     * a campaign for tracking
     *
     * @var Campaign
     */
    private $campaign = null;

    /**
     * should the script block be rendered?
     *
     * @var bool
     */
    private $renderScriptBlock = true;

    /**
     * Content Security Nonce
     *
     * @var null
     */
    private $cspNonce = null;

    /**
     * setting options via constructor
     *
     * @param array $options
     *
     * @throws InvalidArgumentException when tracking id not set
     */
    public function __construct(array $options = [])
    {
        $this->trackingId = array_get($options, 'tracking_id');
        $this->trackingDomain = array_get($options, 'tracking_domain', 'auto');
        $this->trackerName = array_get($options, 'tracker_name', 't0');
        $this->displayFeatures = array_get($options, 'display_features', false);
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

        if (!in_array($hittype, $allowedHitTypes)) {
            return;
        }

        $trackingCode = "ga('send', 'pageview');";

        if ($page !== null || $title !== null || $hittype !== null) {
            $page = ($page === null) ? "window.location.protocol + '//' + window.location.hostname + window.location.pathname + window.location.search" : "'{$page}'";
            $title = ($title === null) ? "document.title" : "'{$title}'";

            $trackingCode = "ga('send', {'hitType': '{$hittype}', 'page': {$page}, 'title': {$title}});";
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
    public function ecommerceAddTransaction(
        $id,
        $affiliation = null,
        $revenue = null,
        $shipping = null,
        $tax = null,
        $currency = null
    )
    {
        // Call to enable ecommerce tracking automatically
        $this->enableEcommerceTracking();

        $parameters = ['id' => $id];

        if (!is_null($affiliation)) {
            $parameters['affiliation'] = $affiliation;
        }

        if (!is_null($revenue)) {
            $parameters['revenue'] = $revenue;
        }

        if (!is_null($shipping)) {
            $parameters['shipping'] = $shipping;
        }

        if (!is_null($tax)) {
            $parameters['tax'] = $tax;
        }

        if (!is_null($currency)) {
            $parameters['currency'] = $currency;
        }

        $jsonParameters = json_encode($parameters);
        $trackingCode = "ga('ecommerce:addTransaction', {$jsonParameters});";

        $this->trackingBag->add($trackingCode);

        return $this;
    }

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
    public function ecommerceAddItem(
        $id,
        $name,
        $sku = null,
        $category = null,
        $price = null,
        $quantity = null,
        $currency = null
    )
    {
        // Call to enable ecommerce tracking automatically
        $this->enableEcommerceTracking();

        $parameters = [
            'id' => $id,
            'name' => $name,
        ];

        if (!is_null($sku)) {
            $parameters['sku'] = $sku;
        }

        if (!is_null($category)) {
            $parameters['category'] = $category;
        }

        if (!is_null($price)) {
            $parameters['price'] = $price;
        }

        if (!is_null($quantity)) {
            $parameters['quantity'] = $quantity;
        }

        if (!is_null($currency)) {
            $parameters['currency'] = $currency;
        }

        $jsonParameters = json_encode($parameters);
        $trackingCode = "ga('ecommerce:addItem', {$jsonParameters});";

        $this->trackingBag->add($trackingCode);

        return $this;
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
     * enable display features
     *
     * @return GoogleAnalytics
     */
    public function enableDisplayFeatures()
    {
        $this->displayFeatures = true;

        return $this;
    }

    /**
     * disable display features
     *
     * @return GoogleAnalytics
     */
    public function disableDisplayFeatures()
    {
        $this->displayFeatures = false;

        return $this;
    }

    /**
     * enable ecommerce tracking
     *
     * @return GoogleAnalytics
     */
    public function enableEcommerceTracking()
    {
        $this->ecommerceTracking = true;

        return $this;
    }

    /**
     * disable ecommerce tracking
     *
     * @return GoogleAnalytics
     */
    public function disableEcommerceTracking()
    {
        $this->ecommerceTracking = false;

        return $this;
    }

    /**
     * enable auto tracking
     *
     * @return GoogleAnalytics
     */
    public function enableAutoTracking()
    {
        $this->autoTrack = true;

        return $this;
    }

    /**
     * disable auto tracking
     *
     * @return GoogleAnalytics
     */
    public function disableAutoTracking()
    {
        $this->autoTrack = false;

        return $this;
    }

    /**
     * render script block
     *
     * @return $this
     */
    public function enableScriptBlock()
    {
        $this->renderScriptBlock = true;

        return $this;
    }

    /**
     * do not render script block
     *
     * @return $this
     */
    public function disableScriptBlock()
    {
        $this->renderScriptBlock = false;

        return $this;
    }

    /**
     * returns the javascript embedding code
     *
     * @return string
     */
    public function render()
    {
        $script[] = $this->_getJavascriptTemplateBlockBegin();

        $trackingUserId = (null === $this->userId)
            ? ''
            : sprintf(", {'userId': '%s'}", $this->userId);

        if ($this->debug) {
            $script[] = "ga('create', '{$this->trackingId}', { 'cookieDomain': 'none' }, '{$this->trackerName}'{$trackingUserId});";
        } else {
            $script[] = "ga('create', '{$this->trackingId}', '{$this->trackingDomain}', '{$this->trackerName}'{$trackingUserId});";
        }

        if ($this->ecommerceTracking) {
            $script[] = "ga('require', 'ecommerce');";
        }

        if ($this->displayFeatures) {
            $script[] = "ga('require', 'displayfeatures');";
        }

        if ($this->anonymizeIp) {
            $script[] = "ga('set', 'anonymizeIp', true);";
        }

        if ($this->nonInteraction) {
            $script[] = "ga('set', 'nonInteraction', true);";
        }

        if ($this->campaign instanceof Campaign) {
            $script[] = (new CampaignRenderer($this->campaign))->render();
        }

        $trackingStack = $this->trackingBag->get();
        if (count($trackingStack)) {
            $script[] = implode("\n", $trackingStack);
        }

        if ($this->autoTrack) {
            $script[] = "ga('send', 'pageview');";
        }

        if ($this->ecommerceTracking) {
            $script[] = "ga('ecommerce:send');";
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
     * make the tracking measurement url insecure
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
    public function trackMeasurementUrl(
        $metricName,
        $metricValue,
        Event $event,
        Campaign $campaign,
        $clientId = null,
        array $params = []
    )
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

        if (isset($params['url'])) {
            unset($params['url']);
        }

        $params = array_merge($defaults['params'], $params);
        $queryParams = [];
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $queryParams[] = sprintf('%s=%s', $key, $value);
            }
        }

        return $url . implode('&', $queryParams);
    }

    /**
     * sets an user id for user tracking
     *
     * @param string $userId
     *
     * @return AnalyticsProviderInterface
     *
     * @see https://developers.google.com/analytics/devguides/collection/analyticsjs/cookies-user-id
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * unset a possible given user id
     *
     * @return AnalyticsProviderInterface
     */
    public function unsetUserId()
    {
        return $this->setUserId(null);
    }

    /**
     * sets custom dimensions
     *
     * @param string|array $dimension
     * @param null|string $value
     * @return AnalyticsProviderInterface
     */
    public function setCustom($dimension, $value = null)
    {
        if ($value === null && is_array($dimension)) {
            $params = json_encode($dimension);
            $trackingCode = "ga('set', $params);";
        } else {
            $trackingCode = "ga('set', '$dimension', '$value');";
        }

        $this->trackCustom($trackingCode);

        return $this;
    }

    /**
     * sets a campaign
     *
     * @param Campaign $campaign
     * @return AnalyticsProviderInterface
     */
    public function setCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * unset a possible given campaign
     *
     * @return AnalyticsProviderInterface
     */
    public function unsetCampaign()
    {
        $this->campaign = null;

        return $this;
    }

    /**
     * enables Content Security Polity and sets nonce
     *
     * @return AnalyticsProviderInterface
     */
    public function withCSP()
    {
        if ($this->cspNonce === null) {
            $this->cspNonce = 'nonce-' . random_int(0, PHP_INT_MAX);
        }

        return $this;
    }

    /**
     * disables Content Security Polity
     *
     * @return AnalyticsProviderInterface
     */
    public function withoutCSP()
    {
        $this->cspNonce = null;

        return $this;
    }

    /**
     * returns the current Content Security Policy nonce
     *
     * @return string|null
     */
    public function cspNonce()
    {
        return $this->cspNonce;
    }

    /**
     * returns start block
     *
     * @return string
     */
    protected function _getJavascriptTemplateBlockBegin()
    {
        $appendix = $this->debug ? '_debug' : '';

        $scriptTag = ($this->cspNonce === null)
            ? '<script>'
            : '<script nonce="' . $this->cspNonce . '">';

        return ($this->renderScriptBlock)
            ? $scriptTag . "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics{$appendix}.js','ga');"
            : '';
    }

    /**
     * returns end block
     *
     * @return string
     */
    protected function _getJavascriptTemplateBlockEnd()
    {
        return ($this->renderScriptBlock)
            ? '</script>'
            : '';
    }

	/**
	 * set a custom tracking ID (the UA-XXXXXXXX-1 code)
	 *
	 * @param string $trackingId
	 *
	 * @return AnalyticsProviderInterface
	 */
	public function setTrackingId( $trackingId ) {
		$this->trackingId = $trackingId;
		return $this;
	}
}
