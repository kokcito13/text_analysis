<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Acme\StoreBundle\Document\Product;
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

//        $dm = $this->get('doctrine_mongodb')->getManager();
//        $dm->persist($product);
//        $dm->flush();

//        $dm = $this->get('doctrine_mongodb')->getManager();
//        $product = $dm->getRepository('AcmeStoreBundle:Product')->findByPrice(19.92);

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

        @$res = file_get_contents('http://analise.lc/app_dev.php/take_info', true, $context);
        if (!isset($res) || !$res) {
            $res = 'Cann\'t conect';
        }

        echo '<pre>';
        var_dump(json_decode($res,true));
        exit;


        return array();
    }

    /**
     * @Route("/take_info", name="take_info")
     * @Method("POST")
     */
    public function saveInfoAction(Request $request)
    {
        $data = (array)$request->request->all();

        return new JsonResponse($data);
    }
}
