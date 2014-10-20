<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Acme\StoreBundle\Document\Product;

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
        $product = $dm->getRepository('AcmeStoreBundle:Product')->findByPrice(19.92);

        echo '<pre>';
        var_dump($product);
//        var_dump($product->getId());
//        var_dump($product->getName());
//        var_dump($product->getPrice());
        exit;

        return $product->toArray();
    }
}
