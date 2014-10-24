<?php
namespace Acme\StoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Acme\StoreBundle\Document\Site;
use Acme\StoreBundle\Document\Task;

/**
 * @MongoDB\Document
 */
class Url
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $uri;

    /**
     * @MongoDB\String
     */
    protected $html;

    /**
     * @MongoDB\String
     */
    protected $content;

    /**
     * @MongoDB\Int
     */
    protected $status;

    /**
     * @MongoDB\Timestamp
     */
    protected $updatedAt;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Site", inversedBy="urls")
     */
    protected $site;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Task", inversedBy="urls")
     */
    protected $task;

    const STATUS_CREATE = 0;
    const STATUS_WITH_HTML = 1;
    const STATUS_WITH_CONTENT = 2;

    public function __construct()
    {
        $this->updatedAt = time();
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
     * Set uri
     *
     * @param string $uri
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Get uri
     *
     * @return string $uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set html
     *
     * @param string $html
     * @return self
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Get html
     *
     * @return string $html
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
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
     * Set updatedAt
     *
     * @param timestamp $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return timestamp $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set site
     *
     * @param Site $site
     * @return self
     */
    public function setSite(Site $site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * Get site
     *
     * @return Site $site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set task
     *
     * @param Task $task
     * @return self
     */
    public function setTask(Task $task)
    {
        $this->task = $task;
        return $this;
    }

    /**
     * Get task
     *
     * @return Task $task
     */
    public function getTask()
    {
        return $this->task;
    }

    public function updateTime()
    {
        $this->updatedAt = time();

        return $this;
    }
}
