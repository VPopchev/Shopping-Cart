<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use AppBundle\Service\FileUploader;
use AppBundle\Service\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
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
            $this->saveProduct($product);
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function saveProduct(Product &$product)
    {
        $file = $product->getImage();
        $fileUploader = new FileUploader('app/images');
        $fileName = $fileUploader->upload($file);
        $product->setImage($fileName);
        $product->setOwner($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        $this->addFlash('success',
            "Product {$product->getName()} added successful!");

    }

    /**
     * @Route("product/view/{id}",name="view_product")
     */
    public function viewAction(Product $product)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser != null && (
                !$currentUser->isEditor() && !$currentUser->isAdmin() && !$currentUser->isOwner($product)) &&
            $product->getIsActive() == false
        ) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('product/view.html.twig', [
            'product' => $product,
        ]);
    }


    /**
     * @Route("product/edit/{id}",name="edit_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function editAction(Product $product, Request $request)
    {
        $baseImage = $product->getImage();
        $product->setImage(New File('./app/images/' . $product->getImage()));

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin()
            && !$currentUser->isEditor())
        {
            return $this->redirectToRoute("homepage");
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->setImage($product, $baseImage);
            return $this->editProduct($product);
        }
        return $this->render('product/edit.html.twig', ['form' => $form->createView()]);
    }

    private function editProduct(Product $product)
    {
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
        $product->setImage(New File('./app/images/' . $product->getImage()));
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

        return $this->render('product/delete.html.twig', ['form' => $form->createView()]);
    }

    private function setImage(Product &$product, $baseImage)
    {
        if (null === $product->getImage()) {
            $product->setImage($baseImage);
        } else {
            $file = $product->getImage();
            $fileUploader = new FileUploader('app/images');
            $fileName = $fileUploader->upload($file);
            $product->setImage($fileName);
        }
    }
}
