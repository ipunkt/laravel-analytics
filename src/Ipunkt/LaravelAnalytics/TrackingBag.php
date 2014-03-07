<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 07.03.14
 */

namespace Ipunkt\LaravelAnalytics;
use Ipunkt\LaravelAnalytics\Contracts\TrackingBagInterface;
use Session;

class TrackingBag implements TrackingBagInterface {

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
		if (Session::has($this->sessionIdentifier))
		{
			$sessionTracks = Session::get($this->sessionIdentifier);
		}

		$sessionTracks[] = $tracking;

		Session::flash($this->sessionIdentifier, $sessionTracks);
	}

	/**
	 * returns all trackings
	 *
	 * @return array
	 */
	public function get()
	{
		if (Session::has($this->sessionIdentifier))
		{
			return Session::get($this->sessionIdentifier);
		}

		return [];
	}
}