<?php

namespace Acme\StoreBundle\Controller;

use Acme\StoreBundle\Document\Site;
use Acme\StoreBundle\Document\Task;
use Acme\StoreBundle\Document\Url;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Acme\StoreBundle\Document\Product;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/prod_test/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
//        $product = new Product();
//        $product->setName($name);
//        $product->setPrice('19.'.rand(10,99));

        $dm = $this->get('doctrine_mongodb')->getManager();
//        $dm->persist($product);
//        $dm->flush();

        $dm = $this->get('doctrine_mongodb')->getManager();
        $tasks = $dm->getRepository('AcmeStoreBundle:Url')->findBy(array('id'=>$name));

        /*
        $arrayData = array(
            array(
                'id' => 1,
                'key' => 'варикоцеле',
                'urls' => array(
                    'http://www.hospital1.ru/varikocelle.htm',
                    'http://ru.wikipedia.org/wiki/Варикоцеле',
                    'http://www.tiensmed.ru/news/varikocele-wkti',
                    'http://www.medicalj.ru/diseases/mens-health/4-varicocele',
                    'http://www.uronet.ru/andrology/varicoc.html',
                    'http://03uro.ru/uropedia/varicocele'
                )
            ),
            array(
                'id' => 2,
                'key' => 'операция варикоцеле',
                'urls' => array(
                    'http://www.uroportal.ru/zabolevaniya/0/15',
                    'http://muzhzdorov.ru/vospaleniya-kozhi-i-organov/varikotsele-posle-operatsii-vy-bor-metoda-lecheniya-bolezni/',
                    'http://www.uronet.ru/andrology/varicoc.html',
                    'http://www.tiensmed.ru/news/varikocele-wkti',
                    'http://venoz.ru/varikocele/varikocele-operacii-varikocele.html'
                )
            )
        );

        $postData = http_build_query(
            $arrayData
        );
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => $postData,
            ),
        ));


        $res = file_get_contents('http://analise.lc/app_dev.php/take_info', true, $context);

        if (!isset($res) || !$res) {
            $res = 'Cann\'t conect';
        }
        */

        echo '<pre>';
        var_dump($tasks[0]->getTitle());
        exit;

        return array();
    }

    /**
     * @Route("/take_info", name="take_info")
     * @Method("POST")
     */
    public function saveInfoAction(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $data = (array)$request->request->all();
        foreach ($data as $value) {
            $task = new Task();
            $task->setOutId($value['id']);
            $task->setKey($value['key']);
            foreach ($value['urls'] as $url) {
                $host = parse_url($url, PHP_URL_HOST);
                $site = $dm->getRepository('AcmeStoreBundle:Site')->findOneByName($host);
                if (!$site) {
                    $site = new Site();
                    $site->setName($host);
                } else {
                    $urlDocument = $site->findUrlByUri($url);
                    if ($urlDocument) {
                        $timeStamp = $urlDocument->getUpdatedAt();
                        $timeTwoWeeks = time()-(14+24+3600);
                        if ($timeTwoWeeks > $timeStamp->__toString()) {
                            $urlDocument->setStatus(Url::STATUS_CREATE);
                        }
                    } else {
                        $urlDocument = new Url();
                        $urlDocument->setUri($url);
                        $site->addUrl($urlDocument);
                        $urlDocument->setSite($site);
                    }
                }
                $urlDocument->setTask($task);
                $task->addUrl($urlDocument);
                $dm->persist($urlDocument);
                $dm->persist($site);
            }
            $dm->persist($task);
        }
        $dm->flush();

        return new JsonResponse($data);
    }


    /**
     * @Route("/istio/{name}")
     * @Template()
     */
    public function istioAction($name)
    {

        $postData = http_build_query(
            array(
                'url'=>'www.tiensmed.ru/news/varikocele-wkti',
                'filter'=>'Отфильтровать текст'
            )
        );
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => $postData,
            ),
        ));


        $res = file_get_contents('http://istio.com/rus/text/analyz/', true, $context);

        $crawler = new Crawler();
        $crawler->addContent($res, 'html');

        $content = $crawler->filter('#mycontent')->text();

        echo '<pre>';
        var_dump(str_replace(PHP_EOL, ' ', $content));
        exit;

    }
}
