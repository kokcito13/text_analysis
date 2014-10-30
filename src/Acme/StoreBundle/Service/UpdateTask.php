<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/24/14
 * Time: 3:24 PM
 */

namespace Acme\StoreBundle\Service;

use Acme\StoreBundle\Document\MorphologyGroup;
use Acme\StoreBundle\Document\Task;
use Acme\StoreBundle\Document\Url;

class UpdateTask {

    private $dm;

    private $reg = array(0=>'/(?<!\pL)', 1=>'(?!\pL)/iu');
    private $regBeetwenWords = '[ .()-:]?\s*';

    public function __construct($dm)
    {
        $this->dm = $dm->getManager();
    }

    public function update()
    {
        $this->updateStatusUrls();
        $this->setLengthTextUrls();
        $this->setCountKey();
    }

    public function updateStatusUrls()
    {
        $qb =  $this->dm
            ->createQueryBuilder('AcmeStoreBundle:Task');
        $qb->addOr($qb->expr()->field('status_urls')->equals(Task::DEFAULT_IN));
        $qb->addOr($qb->expr()->field('status_urls')->exists(false));
        $qb->limit(10);

        $tasks = $qb->getQuery()->execute();
        foreach ($tasks as $task) {/** @var Task $task */
            if (count($task->getUrlsWithContent()) == count($task->getUrls())) {
                $task->setStatusUrls(Task::URLS_PARSE_FINISH);
                $this->dm->persist($task);
            }
        }

        $this->dm->flush();
    }

    public function setLengthTextUrls()
    {
        $qb =  $this->dm
            ->createQueryBuilder('AcmeStoreBundle:Task')
            ->field('status_urls')->equals(Task::URLS_PARSE_FINISH)
            ->field('status')->equals(Task::STATUS_CREATE)
            ->limit(10);

        $tasks = $qb->getQuery()->execute();
        foreach ($tasks as $task) {/** @var Task $task */
            $textLength = $this->getLengthFromUrls($task->getUrls());
            if ($textLength > 0) {
                $task->setTextLength($textLength);
                $task->setStatus(Task::STATUS_SAVE_TEXT_LENGTH);
            }
            $this->dm->persist($task);
        }

        $this->dm->flush();
    }

    public function setCountKey()
    {
        $qb =  $this->dm
            ->createQueryBuilder('AcmeStoreBundle:Task')
            ->field('status')->equals(Task::STATUS_SAVE_TEXT_LENGTH)
            ->limit(10);

        $tasks = $qb->getQuery()->execute();
        foreach ($tasks as $task) {/** @var Task $task */
            $counts = $this->getCountsKey($task->getUrls(), $task->getKey());
            $task->setCountKey($counts);
            $task->setStatus(Task::STATUS_SAVE_COUNT_KEY);
            $this->dm->persist($task);
        }

        $this->dm->flush();
    }

    public function getCountsKey($urls, $key)
    {
        $length = array();
        foreach($urls as $url) {/** @var Url $url */
            $reg = $this->reg[0];
            $reg .= $this->makeRegInSideKey($key);
            $reg .= $this->reg[1];

            $matches = array();
            $q = preg_match_all($reg, $url->getContent(), $matches);
            $length[] = $q;
        }

        $num = array_sum($length)/count($length);

        foreach ($length as $k=>$v) {
            if (($v/2) > $num) {
                unset($length[$k]);
            }
        }
        if (count($length) == 0) {
            return 0;
        }

        return array_sum($length)/count($length);
    }

    public function getLengthFromUrls($urls)
    {
        $length = array();
        foreach($urls as $url) {
            $length[] = mb_strlen($url->getContent(), 'utf8');
        }

        $count = count($length);

        sort($length);
        reset($length);
        if ($count > 4) {
            unset($length[0]);
            unset($length[1]);

            unset($length[$count-2]);
            unset($length[$count-1]);
        }

        $num = array_sum($length)/count($length);

        return $num;
    }

    private function makeRegInSideKey($key)
    {
        $newKey = '';
        $partKey = explode(' ', $key);
        foreach($partKey as $kk=>$vv) {
            if ( ($kk+1) == count($partKey)) {
                $newKey .= $vv;
            } else {
                $newKey .= $vv.$this->regBeetwenWords;
            }
        }
        if (empty($newKey)) {
            $newKey = $key;
        }
        return $newKey;
    }
}