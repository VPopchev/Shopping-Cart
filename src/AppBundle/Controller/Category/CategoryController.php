<?php

namespace AppBundle\Controller\Category;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use AppBundle\Entity\User;
use AppBundle\Form\ProductCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends BaseController
{
    /**
     * @Route("category/manage",name="manage_categories")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function manageCategory(Request $request){
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser->isAdmin() && !$currentUser->isEditor()){
            return $this->redirectToRoute('homepage');
        }
        $category = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class,$category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success',"Category {$category->getName()} created successful!");
            return $this->redirectToRoute('manage_categories');
        }
        foreach($form->getErrors(true) as $error){
            $this->addFlash('error',$error->getMessage());
        }
        return $this->render('category/manage.html.twig',[
            'categories' => $this->categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("category/remove/{id}",name="remove_category")
     */
    public function deleteCategory(ProductCategory $category){
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if(!$currentUser->isAdmin() && !$currentUser->isEditor()){
            return $this->redirectToRoute('homepage');
        }
        /** @var Product $product */
        foreach ($category->getProducts() as $product){
            $product->setCategory(null);
            $product->setStatus('Inactive');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('success',"Category {$category->getName()} deleted successful!");
        return $this->redirectToRoute('manage_categories');
    }




}
