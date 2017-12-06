<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends Controller
{
    /**
     * @Route("category/manage",name="manage_categories")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function manageCategory(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
            return $this->redirectToRoute('homepage');
        }
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveCategory($category);
        }
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();
        return $this->render('category/manage.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("category/remove/{id}",name="remove_category")
     */
    public function deleteCategory(Category $category)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
            return $this->redirectToRoute('homepage');
        }
        /** @var Product $product */
        foreach ($category->getProducts() as $product) {
            $product->setCategory(null);
            $product->setIsActive('Inactive');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', "Category {$category->getName()} deleted successful!");
        return $this->redirectToRoute('manage_categories');
    }

    private function saveCategory(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();
        $this->addFlash('success', "Category {$category->getName()} created successful!");
        return $this->redirectToRoute('manage_categories');
    }


}
