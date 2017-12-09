<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryType;
use AppBundle\Service\Paginator;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends Controller
{
    const PRODUCTS_LIMIT = 6;

    /**
     * @Route("category/manage",name="manage_categories")
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function manageCategory(Request $request)
    {
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
     * @Route("category/list/{id}/{page}",name="category_products_list")
     */
    public function listCategoryProductsAction(Category $category,int $page = 1)
    {
        $offset = ($page - 1) * self::PRODUCTS_LIMIT;
        $products = [];

        $this->recursion($category,$products);

        $allProducts = count($products);
        $products = array_slice($products,$offset,self::PRODUCTS_LIMIT);
        $pages = ceil($allProducts / self::PRODUCTS_LIMIT);

        $paginator = new Paginator($page, $pages, $products);
        return $this->render(':Category:listCategoryProducts.html.twig', [
            'category' => $category,
            'paginator' => $paginator
        ]);
    }

    private function recursion(Category $category,&$products){
        foreach ($category->getActiveProducts() as $activeProd){
            array_push($products,$activeProd);
        }
        if ($category->getChildren()){
            foreach($category->getChildren() as $child){
                $this->recursion($child,$products);
            }
        }
    }

    /**
     * @Route("category/remove/{id}",name="remove_category")
     * @Security("has_role('ROLE_EDITOR')")
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
