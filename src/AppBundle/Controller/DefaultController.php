<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Service\Paginator;
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
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $categories = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->getCategoryWithProducts();
        $currentPage = intval($request->query->get('p') !== null ?
                                    $request->query->get('p') : 1);
        $offset = ($currentPage - 1) * 6;
        $products = $repo->findAllPerPage(6,$offset);
        $allProducts = $repo->count();
        $pages = ceil($allProducts / 6);
        $paginator = new Paginator($currentPage,$pages,$products);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'categories' => $categories,
            'paginator' => $paginator,
        ]);
    }

}
