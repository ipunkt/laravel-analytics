<?php

namespace Ipunkt\LaravelAnalytics\Data\Renderer;

use Ipunkt\LaravelAnalytics\Data\Campaign;

class CampaignRenderer implements Renderer
{
    /**
     * campaign to render
     *
     * @var Campaign
     */
    private $campaign;

    /**
     * CampaignRenderer constructor.
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Renders data
     *
     * @return string
     */
    public function render()
    {
        return $this->renderName()
        . $this->renderSource()
        . $this->renderMedium()
        . $this->renderKeyword()
        . $this->renderContent()
        . $this->renderId();
    }

    /**
     * returns the rendered name
     *
     * @return string
     */
    private function renderName()
    {
        $name = $this->campaign->getName();
        return empty($name) ? '' : "ga('set', 'campaignName', '{$name}');";
    }

    /**
     * returns the rendered source
     *
     * @return string
     */
    private function renderSource()
    {
        $source = $this->campaign->getSource();
        return empty($source) ? '' : "ga('set', 'campaignSource', '{$source}');";
    }

    /**
     * returns the rendered medium
     *
     * @return string
     */
    private function renderMedium()
    {
        $medium = $this->campaign->getMedium();
        return empty($medium) ? '' : "ga('set', 'campaignMedium', '{$medium}');";
    }

    /**
     * returns the rendered keyword
     *
     * @return string
     */
    private function renderKeyword()
    {
        $keyword = $this->campaign->getKeyword();
        return empty($keyword) ? '' : "ga('set', 'campaignKeyword', '{$keyword}');";
    }

    /**
     * returns the rendered content
     *
     * @return string
     */
    private function renderContent()
    {
        $content = $this->campaign->getContent();
        return empty($content) ? '' : "ga('set', 'campaignContent', '{$content}');";
    }

    /**
     * returns the rendered id
     *
     * @return string
     */
    private function renderId()
    {
        $id = $this->campaign->getId();
        return empty($id) ? '' : "ga('set', 'campaignId', '{$id}');";
    }
}
