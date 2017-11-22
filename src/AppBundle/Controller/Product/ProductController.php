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
    public function createProduct(Request $request)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("product/listByCategory/{categoryId}",name="category_list")
     */
    public function listAction(int $categoryId)
    {
        $category = $this->getDoctrine()
            ->getRepository(ProductCategory::class)
            ->find($categoryId);
//
//        $products = $this->getDoctrine()
//            ->getRepository(Product::class)
//            ->findBy(['categoryId' => $categoryId]);
        return $this->render('product/listByCategory.html.twig',
            ['category' => $category
                ]);
    }

    /**
     * @Route("product/view/{id}",name="view_product")
     */
    public function viewProduct(int $id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        return $this->render('product/view.html.twig',
            ['product' => $product]);
    }

    /**
     * @Route("product/edit/{id}",name="edit_product")
     */
    public function editProduct(int $id,Request $request)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->merge($product);
            $em->flush();
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('product/edit.html.twig',[
            'product' => $product,
            'form' => $form->createView()
            ]);
    }
}
