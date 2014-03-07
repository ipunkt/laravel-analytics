<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 07.03.14
 */

namespace Ipunkt\LaravelAnalytics;


use Illuminate\Support\Facades\Facade;

class AnalyticsFacade extends Facade {

	/**
	 * facade accessor
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'analytics';
	}

}