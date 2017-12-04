<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $categories = $this
            ->getDoctrine()
            ->getRepository(ProductCategory::class)
            ->getProductsWithCategory();
        $currentPage = $request->query->get('p') !== null ?
            $request->query->get('p') : 1;
        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->getAllWithCategories();
        $pages = ceil(count($products) / 5);
        $offset = ($currentPage - 1) * 5;
        $products = array_slice($products,$offset,5);
        $paginator = $this->createPaginator($currentPage,$pages,$products);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'categories' => $categories,
            'paginator' => $paginator,
            'products' => $products
        ]);
    }

    private function createPaginator($currentPage,$pages,$products)
    {
        $hasNext = $currentPage < $pages;
        $hasPrevious = $currentPage > 1;

        $paginator = new \AppBundle\Entity\Paginator();
        $paginator->setTasks($products);
        $paginator->setCurrentPage($currentPage);
        $paginator->setAllPages($pages);
        $paginator->setNext($hasNext);
        $paginator->setPrevious($hasPrevious);
        return $paginator;
    }
}
