<?php

namespace Ipunkt\LaravelAnalytics;

use Ipunkt\LaravelAnalytics\Contracts\TrackingBagInterface;
use Session;

/**
 * Class TrackingBag
 *
 * @package Ipunkt\LaravelAnalytics
 */
class TrackingBag implements TrackingBagInterface
{
    /**
     * session identifier
     *
     * @var string
     */
    private $sessionIdentifier = 'analytics.tracking';

    /**
     * adds a tracking
     *
     * @param string $tracking
     */
    public function add($tracking)
    {
        $sessionTracks = [];
        if (Session::has($this->sessionIdentifier)) {
            $sessionTracks = Session::get($this->sessionIdentifier);
        }

        //	prevent duplicates in session
        $trackingKey = md5($tracking);
        $sessionTracks[$trackingKey] = $tracking;

        Session::flash($this->sessionIdentifier, $sessionTracks);
    }

    /**
     * returns all trackings
     *
     * @return array
     */
    public function get()
    {
        $trackings = [];
        if (Session::has($this->sessionIdentifier)) {
            $trackings = Session::get($this->sessionIdentifier);
            Session::forget($this->sessionIdentifier);
        }

        return $trackings;
    }
}
