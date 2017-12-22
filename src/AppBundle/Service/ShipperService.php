<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 21.12.2017 г.
 * Time: 17:40 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shipper;
use AppBundle\Repository\ShipperRepository;
use Doctrine\ORM\EntityManager;

class ShipperService implements ShipperServiceInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ShipperRepository
     */
    private $shipperRepository;

    /**
     * ShipperService constructor.
     * @param EntityManager $entityManager
     * @param ShipperRepository $shipperRepository
     */
    public function __construct(EntityManager $entityManager,
                                ShipperRepository $shipperRepository)
    {
        $this->entityManager = $entityManager;
        $this->shipperRepository = $shipperRepository;
    }


    public function createShipper(Shipper $shipper, Cart $cart, Product $product)
    {
        $shipper->setProduct($product);
        $shipper->setCart($cart);
        $this->entityManager->persist($shipper);
        $this->entityManager->flush();
    }
}