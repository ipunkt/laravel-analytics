<?php

namespace Ipunkt\LaravelAnalytics\Providers;

use Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface;
use Ipunkt\LaravelAnalytics\Data\Campaign;
use Ipunkt\LaravelAnalytics\Data\Event;

/**
 * Class NoAnalytics
 *
 * @package Ipunkt\LaravelAnalytics\Providers
 */
class NoAnalytics implements AnalyticsProviderInterface
{
    /**
     * returns the javascript code for embedding the analytics stuff
     *
     * @return string
     */
    public function render()
    {
        return '';
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
    }

    /**
     * track an event
     *
     * @param string $category
     * @param string $action
     * @param null|string $label
     * @param null|int $value
     *
     * @return void
     */
    public function trackEvent($category, $action, $label = null, $value = null)
    {
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
    }

    /**
     * enable display features
     *
     * @return NoAnalytics
     */
    public function enableDisplayFeatures()
    {
        return $this;
    }

    /**
     * disable display features
     *
     * @return NoAnalytics
     */
    public function disableDisplayFeatures()
    {
        return $this;
    }

    /**
     * enable auto tracking
     *
     * @return NoAnalytics
     */
    public function enableAutoTracking()
    {
        return $this;
    }

    /**
     * disable auto tracking
     *
     * @return NoAnalytics
     */
    public function disableAutoTracking()
    {
        return $this;
    }

    /**
     * render script block
     *
     * @return $this
     */
    public function enableScriptBlock()
    {
        return $this;
    }

    /**
     * do not render script block
     *
     * @return $this
     */
    public function disableScriptBlock()
    {
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
     */
    public function trackMeasurementUrl(
        $metricName,
        $metricValue,
        Event $event,
        Campaign $campaign,
        $clientId = null,
        array $params = []
    ) {
        return '';
    }

    /**
     * sets or gets nonInteraction
     *
     * setting: $this->nonInteraction(true)->render();
     * getting: if ($this->nonInteraction()) echo 'non-interaction set';
     *
     * @param boolean|null $value
     *
     * @return bool|AnalyticsProviderInterface
     */
    public function nonInteraction($value = null)
    {
        if (null === $value) {
            return false;
        }

        return $this;
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
        return $this;
    }

    /**
     * unsets a possible given user id
     *
     * @return AnalyticsProviderInterface
     */
    public function unsetUserId()
    {
        return $this;
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
        return $this;
    }

    /**
     * unsets a possible given campaign
     *
     * @return AnalyticsProviderInterface
     */
    public function unsetCampaign()
    {
        return $this;
    }

    /**
     * enable ecommerce tracking
     *
     * @return AnalyticsProviderInterface
     */
    public function enableEcommerceTracking()
    {
        return $this;
    }

    /**
     * disable ecommerce tracking
     *
     * @return AnalyticsProviderInterface
     */
    public function disableEcommerceTracking()
    {
        return $this;
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
    public function ecommerceAddTransaction($id, $affiliation = null, $revenue = null, $shipping = null, $tax = null, $currency = null)
    {
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
    public function ecommerceAddItem($id, $name, $sku = null, $category = null, $price = null, $quantity = null, $currency = null)
    {
        return $this;
    }

    /**
     * enables Content Security Polity and sets nonce
     *
     * @return AnalyticsProviderInterface
     */
    public function withCSP()
    {
        return $this;
    }

    /**
     * disables Content Security Polity
     *
     * @return AnalyticsProviderInterface
     */
    public function withoutCSP()
    {
        return $this;
    }

    /**
     * returns the current Content Security Policy nonce
     *
     * @return string|null
     */
    public function cspNonce()
    {
        return null;
    }

	/**
	 * set a custom tracking ID (the UA-XXXXXXXX-1 code)
	 *
	 * @param string $trackingId
	 *
	 * @return AnalyticsProviderInterface
	 */
	public function setTrackingId( $trackingId ) {
		return $this;
	}
}
