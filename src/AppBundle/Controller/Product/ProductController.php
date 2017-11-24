<?php

namespace AppBundle\Controller\Product;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends BaseController
{
    /**
     * @Route("product/create", name="create_product")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createAction(Request $request)
    {
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
            'form' => $form->createView(),
            'categories' => $this->categories
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
        return $this->render('product/listByCategory.html.twig', [
            'category' => $category,
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("product/view/{id}",name="view_product")
     */
    public function viewAction(int $id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        return $this->render('product/view.html.twig', [
            'product' => $product,
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("product/edit/{id}",name="edit_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editAction(int $id, Request $request)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        if ($product === null){
            return $this->redirectToRoute("homepage");
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser->isOwner($product) && !$currentUser->isAdmin()){
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($product);
            $em->flush();
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'categories' => $this->categories
        ]);
    }


    /**
     * @Route("product/delete/{id}",name="delete_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction(int $id, Request $request){
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        if($product === null){
            return $this->redirectToRoute("homepage");
        }

        $currentUser = $this->getUser();

        if(!$currentUser->isOwner($product) && !$currentUser->isAdmin()){
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('product/delete.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'categories' => $this->categories
        ]);
    }

}
