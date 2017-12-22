<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 21.12.2017 г.
 * Time: 17:38 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shipper;

interface ShipperServiceInterface
{
    public function createShipper(Shipper $shipper,Cart $cart, Product $product);
}