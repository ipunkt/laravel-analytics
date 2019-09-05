<?php

namespace Ipunkt\LaravelAnalytics;

use Illuminate\Support\Facades\Facade;
use Ipunkt\LaravelAnalytics\Contracts\AnalyticsProviderInterface;

/**
 * Class AnalyticsFacade
 *
 * @package Ipunkt\LaravelAnalytics
 */
class AnalyticsFacade extends Facade
{
    /**
     * facade accessor
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AnalyticsProviderInterface::class;
    }
}