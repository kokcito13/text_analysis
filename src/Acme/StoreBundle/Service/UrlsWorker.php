<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/24/14
 * Time: 3:24 PM
 */

namespace Acme\StoreBundle\Service;


use Acme\StoreBundle\Document\Url;
use Symfony\Component\DomCrawler\Crawler;

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
            ->limit(10)
            ->getQuery()
            ->execute();

        foreach ($urls as $url) { /** @var URl $url */
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                    'timeout' => 120
                ),
            ));
            $res = file_get_contents($url->getUri(), false, $context);

            $crawler = new Crawler();
            $crawler->addContent($res, 'html');

            $url->setHtml($crawler->html());
            $url->setStatus(Url::STATUS_WITH_HTML);
            $this->dm->persist($url);

            echo $url->getId().',';
        }
        $this->dm->flush();
    }
} 