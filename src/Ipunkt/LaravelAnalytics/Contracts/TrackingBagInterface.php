<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 07.03.14
 */

namespace Ipunkt\LaravelAnalytics\Contracts;


interface TrackingBagInterface {

	/**
	 * adds a tracking
	 *
	 * @param string $tracking
	 * @return void
	 */
	public function add($tracking);

	/**
	 * returns all trackings
	 *
	 * @return array
	 */
	public function get();

}