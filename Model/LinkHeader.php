<?php

namespace Bazinga\Bundle\RestExtraBundle\Model;

class LinkHeader
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $rel;

    /**
     * @param string $url
     * @param string $rel
     */
    public function __construct($url, $rel)
    {
        $this->url = trim($url);
        $this->rel = trim($rel);
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @return boolean
     */
    public function hasRel()
    {
        return !empty($this->rel);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
