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

class UpdateTask {

    private $dm;


    public function __construct($dm)
    {
        $this->dm = $dm->getManager();
    }

    public function update()
    {
        $this->updateStatusUrls();
        $this->setLengthTextUrls();
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
            ->limit(10);

        $tasks = $qb->getQuery()->execute();
        foreach ($tasks as $task) {/** @var Task $task */
            $textLength = $this->getLengthFromUrls($task->getUrls());
            if ($textLength > 0) {
                $task->setTextLength($textLength);
            }
            $this->dm->persist($task);
        }

        $this->dm->flush();
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

}