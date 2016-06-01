<?php

namespace Ipunkt\LaravelAnalytics\Data;

/**
 * Class Campaign
 *
 * @package Ipunkt\LaravelAnalytics\Data
 */
class Campaign
{
    /**
     * campaign source
     *
     * @var string
     */
    private $source = 'newsletter';

    /**
     * campaign medium
     *
     * @var string
     */
    private $medium = 'email';

    /**
     * campaign name
     *
     * @var string
     */
    private $name;

    /**
     * campaign keyword
     *
     * @var string
     */
    private $keyword;

    /**
     * campaign content
     *
     * @var string
     */
    private $content;

    /**
     * campaign id
     *
     * @var string
     */
    private $id;

    /**
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->name = $name;
    }

    /**
     * set medium
     *
     * @param string $medium
     *
     * @return Campaign
     */
    public function setMedium($medium)
    {
        $this->medium = $medium;

        return $this;
    }

    /**
     * @return string
     */
    public function getMedium()
    {
        return $this->medium;
    }

    /**
     * set name
     *
     * @param string $name
     *
     * @return Campaign
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set source
     *
     * @param string $source
     *
     * @return Campaign
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * returns Keyword
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * sets keyword
     *
     * @param string $keyword
     * @return $this
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
        return $this;
    }

    /**
     * returns Content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * sets content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * returns Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * sets id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
