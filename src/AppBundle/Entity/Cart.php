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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product")
     * @ORM\JoinTable(name="products_carts",
     *     joinColumns={@ORM\JoinColumn(name="cart_id",referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="product_id",referencedColumnName="id",onDelete="CASCADE")})
     */
    private $products;


    public function __construct()
    {
        $this->products = [];
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
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;
    }


    public function getTotal(User $user)
    {
        $sum = 0;
        foreach ($this->products as $product) {
            if ($product->getTopPromotion($user)) {
                $productPrice = $product->getPrice();
                $discount = $product->getTopPromotion($user)->getDiscount();
                $promotionPrice = $productPrice - ($productPrice / 100.0) * $discount;

                $sum += $promotionPrice;
            } else {
                $sum += $product->getPrice();
            }
        }
        return $sum;
    }

    public function clear()
    {
        $this->products = [];
    }
}

