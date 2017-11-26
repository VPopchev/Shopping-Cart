<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['status' => 'Active']);
        foreach ($products as $product){
            $category = $this->getDoctrine()
                ->getRepository(ProductCategory::class)
                ->find($product->getCategoryId());
            $owner = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($product->getUserId());
            $product->setCategory($category);
            $product->setOwner($owner);
        }
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'products' => $products,
            'categories' => $this->categories
        ]);
    }
}
