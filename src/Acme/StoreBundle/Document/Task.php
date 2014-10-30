<?php
namespace Acme\StoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Acme\StoreBundle\Document\Url;
use Acme\StoreBundle\Document\Site;
use Acme\StoreBundle\Document\MorphologyGroup;

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
    protected $status_morphology;

    /**
     * @MongoDB\Int
     */
    protected $status_urls;

    /**
     * @MongoDB\Int
     */
    protected $outId;

    /**
     * @MongoDB\Int
     */
    protected $textLength;

    /**
     * @MongoDB\Int
     */
    protected $countKey;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Url", mappedBy="task")
     */
    protected $urls;

    /**
     * @MongoDB\ReferenceOne(targetDocument="MorphologyGroup", inversedBy="task")
     */
    protected $morphologyGroup;

    const DEFAULT_IN = 0;

    const STATUS_CREATE = 0;
    const STATUS_SAVE_TEXT_LENGTH = 1;
    const STATUS_SAVE_COUNT_KEY = 2;
    const STATUS_DONE = 9;

    const MORPHOLOGY_DONE = 1;

    const URLS_PARSE_FINISH = 1;

    public function __construct()
    {
        $this->urls = new ArrayCollection();
        $this->createdAt = time();

        $this->textLength = 0;
        $this->countKey = 0;

        $this->status = self::STATUS_CREATE;
        $this->status_morphology = self::DEFAULT_IN;
        $this->status_urls = self::DEFAULT_IN;
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

    public function getUrlsWithContent()
    {
        $arr = array();
        foreach ($this->getUrls() as $url) {
            if ($url->getStatus() == Url::STATUS_WITH_CONTENT) {
                $arr[] = $url;
            }
        }

        return $arr;
    }

    /**
     * Set statusMorphology
     *
     * @param int $statusMorphology
     * @return self
     */
    public function setStatusMorphology($statusMorphology)
    {
        $this->status_morphology = $statusMorphology;
        return $this;
    }

    /**
     * Get statusMorphology
     *
     * @return int $statusMorphology
     */
    public function getStatusMorphology()
    {
        return $this->status_morphology;
    }

    /**
     * Set statusUrls
     *
     * @param int $statusUrls
     * @return self
     */
    public function setStatusUrls($statusUrls)
    {
        $this->status_urls = $statusUrls;
        return $this;
    }

    /**
     * Get statusUrls
     *
     * @return int $statusUrls
     */
    public function getStatusUrls()
    {
        return $this->status_urls;
    }

    /**
     * Set morphologyGroup
     *
     * @param MorphologyGroup $morphologyGroup
     * @return self
     */
    public function setMorphologyGroup(MorphologyGroup $morphologyGroup)
    {
        $this->morphologyGroup = $morphologyGroup;
        return $this;
    }

    /**
     * Get morphologyGroup
     *
     * @return MorphologyGroup $morphologyGroup
     */
    public function getMorphologyGroup()
    {
        return $this->morphologyGroup;
    }

    /**
     * Set textLength
     *
     * @param int $textLength
     * @return self
     */
    public function setTextLength($textLength)
    {
        $this->textLength = $textLength;
        return $this;
    }

    /**
     * Get textLength
     *
     * @return int $textLength
     */
    public function getTextLength()
    {
        return $this->textLength;
    }

    /**
     * Set countKey
     *
     * @param int $countKey
     * @return self
     */
    public function setCountKey($countKey)
    {
        $this->countKey = $countKey;
        return $this;
    }

    /**
     * Get countKey
     *
     * @return int $countKey
     */
    public function getCountKey()
    {
        return $this->countKey;
    }
}
