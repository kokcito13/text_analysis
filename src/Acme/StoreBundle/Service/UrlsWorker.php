<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/24/14
 * Time: 3:24 PM
 */

namespace Acme\StoreBundle\Service;


use Acme\StoreBundle\Document\Url;

class UrlsWorker {

    private $dm;

    public function __construct($dm)
    {
        $this->dm = $dm->getManager();
    }

    public function getHtml()
    {
        $urls =  $this->dm
            ->createQueryBuilder('AcmeStoreBundle:Url')
            ->field('status')->equals(Url::STATUS_CREATE)
            ->limit(1)
            ->getQuery()
            ->execute();

        foreach ($urls as $url) { /** @var URl $url */
            $page = file_get_contents($url->getUri());
            $page = mb_convert_encoding($page, "UTF-8");
            $url->setHtml($page);
            $url->setStatus(Url::STATUS_WITH_HTML);
            $this->dm->persist($url);
        }
        $this->dm->flush();

//        echo '<pre>';
//        var_dump($page);
//        exit;
    }
} 