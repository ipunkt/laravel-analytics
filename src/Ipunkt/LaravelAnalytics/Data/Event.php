<?php namespace Ipunkt\LaravelAnalytics\Data;

/**
 * Class Event
 * @package Ipunkt\LaravelAnalytics\Data
 */
class Event
{
	/**
	 * event category
	 *
	 * @var string
	 */
	private $category = 'email';

	/**
	 * event action
	 *
	 * @var string
	 */
	private $action = 'open';

	/**
	 * event label
	 *
	 * @var string
	 */
	private $label;

	/**
	 * hit type
	 *
	 * @var string
	 */
	private $hitType = 'event';

	/**
	 * set action
	 *
	 * @param string $action
	 *
	 * @return Event
	 */
	public function setAction($action)
	{
		$this->action = $action;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * set category
	 *
	 * @param string $category
	 *
	 * @return Event
	 */
	public function setCategory($category)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * set hitType
	 *
	 * @param string $hitType
	 *
	 * @return Event
	 */
	public function setHitType($hitType)
	{
		$this->hitType = $hitType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getHitType()
	{
		return $this->hitType;
	}

	/**
	 * set label
	 *
	 * @param string $label
	 *
	 * @return Event
	 */
	public function setLabel($label)
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}
}