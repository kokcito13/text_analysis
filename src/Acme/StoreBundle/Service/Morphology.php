<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/24/14
 * Time: 3:24 PM
 */

namespace Acme\StoreBundle\Service;

class Morphology {

    private $dm;

    private $morphologyUrl = 'http://kokcito.ddns.ukrtel.net/morf/?word=';

    public function __construct($dm)
    {
        $this->dm = $dm->getManager();
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


        echo '<pre>';
        var_dump($data);
        exit;
        return $data;
    }
}