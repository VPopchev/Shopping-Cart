<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="carts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 */
class Cart
{
    /**
     * @var int
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CartProducts",mappedBy="cartId")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var Product[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Shipper",mappedBy="cart")
     *
     */
    private $shipper;


    public function __construct()
    {
        $this->shipper = [];
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return ArrayCollection|Product[]
     */
    public function getShipper()
    {
        return $this->shipper;
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        $this->shipper[] = $product;
    }


    public function getTotal(User $user)
    {
        $sum = 0;
        foreach ($this->shipper as $shipper) {
            if ($shipper->getProduct()->getTopPromotion($user)) {
                $productPrice = $shipper->getProduct()->getPrice();
                $discount = $shipper->getProduct()->getTopPromotion($user)->getDiscount();
                $promotionPrice = $productPrice - ($productPrice / 100.0) * $discount;

                $sum += $promotionPrice * $shipper->getQuantity();
            } else {
                $sum += $shipper->getProduct()->getPrice() * $shipper->getQuantity();
            }
        }
        return $sum;
    }

    public function clear()
    {
        $this->shipper = new ArrayCollection();
    }
}

