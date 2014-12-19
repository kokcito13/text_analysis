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
        if (count($urls) == 0) return;
        foreach ($urls as $url) { /** @var URl $url */
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                    'timeout' => 120
                ),
            ));

            $url->setHtml("NOT CORRECT URL");
            if (filter_var($url->getUri(), FILTER_VALIDATE_URL)) {
                $res = file_get_contents($url->getUri(), false, $context);

                $crawler = new Crawler();
                $crawler->addContent($res, 'html');
                $url->setHtml($crawler->html());
            }

            $url->setStatus(Url::STATUS_WITH_HTML);
            $this->dm->persist($url);

            echo $url->getId().',';
        }
        $this->dm->flush();
    }


    public function getContent()
    {
        $urls =  $this->dm
            ->createQueryBuilder('AcmeStoreBundle:Url')
            ->field('status')->equals(Url::STATUS_WITH_HTML)
            ->limit(10)
            ->getQuery()
            ->execute();
        if (count($urls) == 0) return;
        foreach ($urls as $url) { /** @var URl $url */
            $uri = preg_replace('/https|http:\/\//iu','',$url->getUri());
            $postData = http_build_query(
                array(
                    'ParseTextForm[url]'=>$uri,
                    'filter'=>'Фильтровать текст'
                )
            );
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                    'content' => $postData,
                ),
            ));

            sleep(5);
            $res = file_get_contents('http://istio.com/rus/text/analyz/', true, $context);

            $crawler = new Crawler();
            $crawler->addContent($res, 'html');

            $content = $crawler->filter('#ContentForm_content')->text();
//            $content = preg_replace('\\r','',$content);

            $url->setContent(str_replace(PHP_EOL, ' ', $content));
            $url->setStatus(Url::STATUS_WITH_CONTENT);
            $this->dm->persist($url);

            echo $url->getId().',';
        }
        $this->dm->flush();
    }
} 