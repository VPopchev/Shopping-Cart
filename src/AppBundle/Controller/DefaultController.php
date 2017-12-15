<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Service\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    const PRODUCTS_LIMIT = 6;

    /**
     * @Route("/{page}", name="homepage")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(int $page = 1)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $categories = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->getMainCategoriesWithProducts();

        $offset = ($page - 1) * self::PRODUCTS_LIMIT;
        $products = $repo->findAllPerPage(self::PRODUCTS_LIMIT,$offset);
        $allProducts = $repo->count();
        $pages = ceil($allProducts / self::PRODUCTS_LIMIT);
        $paginator = new Paginator($page,$pages,$products);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'categories' => $categories,
            'paginator' => $paginator,
        ]);
    }
}
