<?php
/**
 * ipunkt/laravel-analytics
 *
 * @author rok
 * @since 07.03.14
 */

return array(

	/**
	 * current selected provider
	 */
	'provider' => 'GoogleAnalytics',

	/**
	 * configurations for all possible providers
	 */
	'configurations' => [

		'GoogleAnalytics' => [

			/**
			 * Tracking ID
			 */
			'tracking_id' => 'UA-XXXXXXXX-1',

			/**
			 * Tracking Domain
			 */
			'tracking_domain' => 'auto',

			/**
			 * Use ip anonymized
			 */
			'anonymize_ip' => true,

			/**
			 * Auto tracking pageview: ga('send', 'pageview');
			 * If false, you have to do it manually for each request
			 * Or you can use Analytics::disableAutoTracking(), Analytics::enableAutoTracking()
			 */
			'auto_track' => true,

		]

	],

);