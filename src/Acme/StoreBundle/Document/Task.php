<?php
namespace Acme\StoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Acme\StoreBundle\Document\Url;
use Acme\StoreBundle\Document\Site;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 */
class Task
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $key;

    /**
     * @MongoDB\Timestamp
     */
    protected $createdAt;

    /**
     * @MongoDB\Int
     */
    protected $status;

    /**
     * @MongoDB\Int
     */
    protected $outId;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Url", mappedBy="task")
     */
    protected $urls;

    const STATUS_CREATE = 0;

    public function __construct()
    {
        $this->urls = new ArrayCollection();
        $this->createdAt = time();

        $this->status = self::STATUS_CREATE;
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
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
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
     * Set status
     *
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set outId
     *
     * @param int $outId
     * @return self
     */
    public function setOutId($outId)
    {
        $this->outId = $outId;
        return $this;
    }

    /**
     * Get outId
     *
     * @return int $outId
     */
    public function getOutId()
    {
        return $this->outId;
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
}
