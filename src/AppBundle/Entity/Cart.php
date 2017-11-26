<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
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
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;


    /**
     * @var User
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="cart")
     * @ORM\JoinColumn(name="userId",referencedColumnName="id")
     */
    private $user;

    /**
     * @var Product[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product")
     * @ORM\JoinTable(name="products_carts",
     *     joinColumns={@ORM\JoinColumn(name="cart_id",referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="product_id",referencedColumnName="id",onDelete="CASCADE")})
     */
    private $products;

    /**
     * Cart constructor.
     * @param Product[]|ArrayCollection $products
     */
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
     * Set userId
     *
     * @param string $userId
     *
     * @return Cart
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return ArrayCollection|Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product[]|ArrayCollection $products
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


    public function getTotal(){
        $sum = 0;
        foreach ($this->products as $product){
            $sum += $product->getPrice();
        }
        return $sum;
    }
    public function clearCart(){
        $this->products = [];
    }
}

