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
    public function trackPage($page, $title, $hittype)
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
    public function trackEvent($category, $action, $label, $value)
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
}