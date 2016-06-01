<?php

namespace Ipunkt\LaravelAnalytics\Data\Renderer;

/**
 * Interface Renderer
 * @package Ipunkt\LaravelAnalytics\Data\Renderer
 */
interface Renderer
{
    /**
     * Renders data
     *
     * @return string
     */
    public function render();
}