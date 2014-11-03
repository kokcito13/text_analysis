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

class Morphology {

    private $dm;

    private $morphologyUrl = 'http://kokcito.ddns.ukrtel.net/morf/?word=';

    public function __construct($dm, $morphologyUrl = '')
    {
        $this->dm = $dm->getManager();
        if (!empty($morphologyUrl))
            $this->morphologyUrl = $morphologyUrl.'/?word=';
    }

    public function getGroup()
    {
        echo $this->morphologyUrl;

        $qb =  $this->dm
            ->createQueryBuilder('AcmeStoreBundle:Task');
        $qb->addOr($qb->expr()->field('status_morphology')->equals(Task::DEFAULT_IN));
        $qb->addOr($qb->expr()->field('status_morphology')->exists(false));
        $qb->limit(10);

        $tasks = $qb->getQuery()->execute();
        foreach ($tasks as $task) {/** @var Task $task */
            $morph = $this->dm->getRepository('AcmeStoreBundle:MorphologyGroup')->findOneByKey($task->getKey());
            if (!$morph) {
                $words = array();
                $parts = explode(' ',$task->getKey());
                foreach ($parts as $part) {
                    $words = array_merge($words, $this->getWords($part));
                }
                $morph = new MorphologyGroup();
                $morph->setKey($task->getKey());
                $morph->setKeys(json_encode($words));
                $this->dm->persist($morph);
            }

            $task->setMorphologyGroup($morph);
            $task->setStatusMorphology(Task::MORPHOLOGY_DONE);
            $this->dm->persist($task);
        }

        $this->dm->flush();
    }

    public function getWords($word)
    {
        $data = array();
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL
            ),
        ));

        $res = file_get_contents($this->morphologyUrl.$word, true, $context);

        if ($res) {
            if (!empty($res)) {
                $data = json_decode($res, true);
            } else {
                $data['error'] = 'Host return empty';
            }
        } else {
            $data['error'] = 'Host cann\'t open';
        }

        return $data;
    }
}