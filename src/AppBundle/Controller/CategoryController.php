<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryType;
use AppBundle\Service\CategoryServiceInterface;
use AppBundle\Service\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends Controller
{
    const PRODUCTS_LIMIT = 6;

    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;


    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }


    /**
     * @Route("category/manage",name="manage_categories")
     * @Security("has_role('ROLE_EDITOR')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function manageCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->create($category);
            $this->addFlash('success', "Category {$category->getName()} created successful!");
            return $this->redirectToRoute('manage_categories');

        }
        $categories = $this->categoryService->getAllCategories();

        return $this->render('category/manage.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("category/list/{id}/{page}",name="category_products_list")
     * @param Category $category
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoryProductsAction(Category $category,int $page = 1)
    {

        $offset = ($page - 1) * self::PRODUCTS_LIMIT;

        $productsCount = $this->categoryService->getProductsCount($category->getid());
        $products = $this->categoryService->listCategoryProducts(self::PRODUCTS_LIMIT,$offset,$category->getId());

        $pages = ceil($productsCount / self::PRODUCTS_LIMIT);

        $paginator = new Paginator($page, $pages, $products);
        return $this->render(':Category:listCategoryProducts.html.twig', [
            'category' => $category,
            'paginator' => $paginator
        ]);
    }


    /**
     * @Route("category/remove/{id}",name="remove_category")
     * @Security("has_role('ROLE_EDITOR')")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategory(Category $category)
    {
        $this->categoryService->delete($category);
        $this->addFlash('success', "Category deleted successful!");
        return $this->redirectToRoute('manage_categories');
    }


}
