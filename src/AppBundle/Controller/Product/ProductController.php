<?php

namespace AppBundle\Controller\Product;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    /**
     * @Route("product/create", name="create_product")
     */
    public function createProduct(Request $request){
        $product = new Product();

        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $product->setOwner($this->getUser());
            $category = $this
                ->getDoctrine()
                ->getRepository(ProductCategory::class)
                ->find($product->getCategoryId());
            $product->setCategory($category);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('product/create.html.twig',[
           'form' => $form->createView()
        ]);

    }
}
