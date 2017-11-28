<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseController
 * @package AppBundle\Controller
 * @Route(service="base_controller")
 */
class BaseController extends Controller
{
    protected $categories;

    public function __construct(EntityManager $em)
    {
        $this->categories = $em->getRepository(ProductCategory::class)
            ->findAll();
    }
}
