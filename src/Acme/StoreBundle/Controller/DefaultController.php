<?php

namespace Acme\StoreBundle\Controller;

use Acme\StoreBundle\Document\MorphologyGroup;
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

//        $dm->persist($product);
//        $dm->flush();

        $dm = $this->get('doctrine_mongodb')->getManager();
        $tasks = $dm->getRepository('AcmeStoreBundle:Url')->find();

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


        $res = file_get_contents('http://analise.lc/app_dev.php/take_info', false, $context);

        if (!isset($res) || !$res) {
            $res = 'Cann\'t conect';
        }*/


        echo '<pre>';
//        var_dump($res);
        var_dump($tasks[0]->getTitle());
        exit;

        return array();
    }

    /**
     * @Route("/take_info", name="take_info")
     */
    public function saveInfoAction(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $data = (array)$request->request->all();

        $resp = array();
        try {
        foreach ($data as $value) {
            $task = new Task();
            $task->setOutId($value['id']);
            $task->setKey($value['key']);
            foreach ($value['urls'] as $url) {
                $url = trim($url);
                $host = parse_url($url, PHP_URL_HOST);
                $site = $dm->getRepository('AcmeStoreBundle:Site')->findOneByName($host);
                if (!$site) {
                    $site = new Site();
                    $site->setName($host);
                    $urlDocument = new Url();
                    $urlDocument->setUri($url);
                    $site->addUrl($urlDocument);
                    $urlDocument->setSite($site);
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
            $resp['success'] = true;
        } catch(\Exception $e) {
            $resp['error'] = $e->getMessage();
        }

        return new JsonResponse($resp);
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

    /**
     * @Route("/morph/{id}")
     * @Template()
     */
    public function morphAction($id)
    {
//        $morf = new MorphologyGroup();
//        $morf->setKey('test1111');
//        $morf->setKeys(json_encode(array('somesing')));

        $dm = $this->get('doctrine_mongodb')->getManager();
//        $dm->persist($morf);
//        $dm->flush();

        $morf = $dm->getRepository('AcmeStoreBundle:MorphologyGroup')->find($id);

        echo '<pre>';
        var_dump($morf->getKeys());
        exit;
    }

    /**
     * @Route("/", name="sample_page_hello")
     * @Template()
     */
    public function helloAction()
    {
        echo 122;

        return array();
    }

    /**
     * @Route("/get/{id}", name="get_by_out_id")
     */
    public function getAction($id)
    {
        $resp = array();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $tasks = $dm->getRepository('AcmeStoreBundle:Task')->findBy(
            array(
                'outId'=>(int)$id,
                'status' => Task::STATUS_DONE
            ));

        $tasksAll = $dm->getRepository('AcmeStoreBundle:Task')->findBy(
            array(
                'outId'=>(int)$id
            ));

        $result = array();
        foreach ($tasks as $task) {/** @var Task $task */
            $result[] = array (
                'key' => $task->getKey(),
                'status' => $task->getStatus(),
                'text_length' => $task->getTextLength(),
                'count_key' => $task->getCountKey()
            );
        }

        if (!$tasks || count($tasksAll) > count($tasks) ) {
            $resp['error'] = 'Empty or not isset';
        } else {
            $resp['data'] = $result;
            $resp['success'] = true;
        }

        return new JsonResponse($resp);
    }

    /**
     * @Route("/get_full/{id}", name="get_full_by_out_id")
     * @Template()
     */
    public function getFullAction($id)
    {
        $resp = array();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $tasks = $dm->getRepository('AcmeStoreBundle:Task')->findBy(
            array(
                'outId'=>(int)$id,
                'status' => Task::STATUS_DONE
            ));

        $result = array();
        if ($tasks) {
            foreach ($tasks as $task) {/** @var Task $task */
                $keys = explode(' ', $task->getKey());
                $urls = array();
                $urlArray = $task->getUrls();
                if ($urlArray) {
                    $i = 0;
                    foreach ($urlArray as $url) {/** @var Url $url */
                        $urls[$i] = array();
                        $urls[$i]['url'] = $url->getUri();
                        $urls[$i]['keys'] = array();

                        $content = $url->getContent();
                        $urls[$i]['length'] = strlen($content);

                        $urls[$i]['key'] = array(
                            'name' => $task->getKey(),
                            'count' => $this->getKeyCount($task->getKey(), $content)
                        );

                        foreach ($keys as $key) {
                            $urls[$i]['keys'][] = array(
                                'name' => $key,
                                'count' => $this->getKeyCount($key, $content)
                            );
                        }
                        $i++;
                    }
                }
                $result[] = array (
                    'key' => $task->getKey(),
//                    'status' => $task->getStatus(),
                    'text_length' => $task->getTextLength(),
                    'count_key' => $task->getCountKey(),
                    'urls' => $urls
                );
            }
        } else {
            echo '<pre>';
            var_dump("NOT IN");
            exit;
        }

        return array('result'=>$result);
    }

    public function getKeyCount($key, $content)
    {
        $reg = '/(?<!\pL)';
        $reg .= $this->makeRegInSideKey($key);
        $reg .= '(?!\pL)/iu';

        $matches = array();
        $q = preg_match_all($reg, $content, $matches);

        return $q;
    }

    private function makeRegInSideKey($key)
    {
        $key = mb_strtolower($key, 'utf8');
        $key = str_replace('ё', 'е', $key);

        $newKey = '';
        $partKey = explode(' ', $key);
        foreach($partKey as $kk=>$vv) {
            if ( ($kk+1) == count($partKey)) {
                $newKey .= $vv;
            } else {
                $newKey .= $vv.'[ .()-:]?\s*';
            }
        }
        if (empty($newKey)) {
            $newKey = $key;
        }

        return $newKey;
    }
}
