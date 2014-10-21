<?php
namespace Acme\StoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Acme\StoreBundle\Document\Url;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 */
class Site
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $name;

    /**
     * @MongoDB\Timestamp
     */
    protected $createdAt;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Url", mappedBy="site")
     */
    protected $urls;

    public function __construct()
    {
        $this->urls = new ArrayCollection();
        $this->createdAt = time();
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param timestamp $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return timestamp $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add url
     *
     * @param Url $url
     */
    public function addUrl(Url $url)
    {
        $this->urls[] = $url;
    }

    /**
     * Remove url
     *
     * @param Url $url
     */
    public function removeUrl(Url $url)
    {
        $this->urls->removeElement($url);
    }

    /**
     * Get urls
     *
     * @return ArrayCollection $urls
     */
    public function getUrls()
    {
        return $this->urls;
    }

    public function findUrlByUri($uri)
    {
        $urls = $this->getUrls();
        foreach ($urls as $url) {
            if ($url->getUri() === $uri) {
                return $url;
            }
        }

        return null;
    }
}
