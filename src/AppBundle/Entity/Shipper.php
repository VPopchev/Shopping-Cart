<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipper
 *
 * @ORM\Table(name="shippers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipperRepository")
 */
class Shipper
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product",inversedBy="cartProducts",cascade={"persist"})
     * @ORM\JoinColumn(name="product_id",referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $product;

    /**
     * @var Cart
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cart",inversedBy="shipper")
     * @ORM\JoinColumn(name="cart_id",referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $cart;

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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Shipper
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * @param Cart $cart
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }


}

