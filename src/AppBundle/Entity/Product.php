<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @Assert\NotBlank()
     * @ORM\Column(name="categoryId", type="integer",nullable=true)
     */
    private $categoryId;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Product name cannot be empty!"
     * )
     * @Assert\Length(
     *     min="3",
     *     minMessage="Product name must be a least {{ limit }} characters long!"
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Product description cannot be empty!"
     * )
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float
     * @Assert\Range(
     *     min="0",
     *     minMessage="Product Price cannot be less than {{ limit }}"
     * )
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     * @ORM\Column(name="quantity",type="integer")
     *  @Assert\Range(
     *     min="1",
     *     max="1000",
     *     minMessage="Product quantity must be at least {{ limit }}!",
     *     maxMessage="Product quantity cannot be more than {{ limit }}"
     * )
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;


    /**
     * @var ProductCategory
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductCategory"), inversedBy="products")
     * @ORM\JoinColumn(name="categoryId",referencedColumnName="id",onDelete="SET NULL")
     */
    private $category;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="userId",referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\Column(name="status",type="string")
     */
    private $status;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }




    /**
     * @param User $user
     * @return Product
     */
    public function setOwner(User $user){
        $this->owner = $user;
        return $this;
    }

    public function getOwner(){
        return $this->owner;
    }


    /**
     * @param ProductCategory|null $category
     * @return Product
     */
    public function setCategory(?ProductCategory $category){
            $this->category = $category;
            return $this;
    }

    public function getCategory(){
        return $this->category;
    }


    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Product
     */
    public function setQuantity($quantity)
    {
        if($quantity <= 0){
            $this->setStatus('Inactive');
            $this->quantity = 0;
            return $this;
        }
        $this->quantity = $quantity;
        return $this;
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
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return Product
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Product
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }


}

