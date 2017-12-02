<?php

namespace AppBundle\Controller\Product;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
            return $this->saveProduct($product);
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $this->categories
        ]);
    }

    private function saveProduct(Product $product)
    {
        $product->setOwner($this->getUser());
        $category = $this
            ->getDoctrine()
            ->getRepository(ProductCategory::class)
            ->find($product->getCategoryId());
        $product->setCategory($category);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        $this->addFlash('success',
            "Product {$product->getName()} added successful!");
        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Route("product/listByCategory/{id}",name="category_list")
     */
    public function listAction(ProductCategory $category)
    {
        return $this->render('product/listByCategory.html.twig', [
            'category' => $category,
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("product/view/{id}",name="view_product")
     */
    public function viewAction(Product $product, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if((!$currentUser->isEditor() && !$currentUser->isAdmin() && !$currentUser->isOwner($product)) &&
            $product->getStatus() == 'Inactive'){
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        return $this->render('product/view.html.twig', [
            'product' => $product,
            'categories' => $this->categories,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("product/edit/{id}",name="edit_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editAction(Product $product, Request $request)
    {
        if ($product === null) {
            return $this->redirectToRoute("homepage");
        }
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin() && !$currentUser->isEditor()) {
            return $this->redirectToRoute("homepage");
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->editProduct($product);
        }
        return $this->render('product/edit.html.twig', [
            'product' => $product, 'form' => $form->createView(),
            'categories' => $this->categories]);
    }

    private function editProduct(Product $product)
    {
        $category = $this->getDoctrine()
            ->getRepository(ProductCategory::class)
            ->find($product->getCategoryId());
        $product->setCategory($category);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $this->addFlash('success',
            "Product {$product->getName()} successful edited!");
        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Route("product/delete/{id}",name="delete_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction(Product $product, Request $request)
    {
        if (null === $product) {
            return $this->redirectToRoute("homepage");
        }
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin() && !$currentUser->isEditor()) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', "Product {$product->getName()} successful deleted!");
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('product/delete.html.twig', [
            'product' => $product, 'form' => $form->createView(),
            'categories' => $this->categories]);
    }


}
