<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
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
     * @Assert\Range(
     *     min="1",
     *     max="1000",
     *     minMessage="Product quantity must be at least {{ limit }}!",
     *     maxMessage="Product quantity cannot be more than {{ limit }}"
     * )
     */
    private $quantity;


    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category"), inversedBy="products")
     * @ORM\JoinColumn(name="categoryId",referencedColumnName="id",onDelete="SET NULL",nullable=true)
     */
    private $category;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="userId",referencedColumnName="id",onDelete="CASCADE")
     */
    private $owner;

    /**
     * @ORM\Column(name="status",type="boolean")
     */
    private $isActive;


    /**
     * @ORM\Column(type="string")
     * @Assert\File(mimeTypes={"image/jpeg"},maxSize="5500k")
     */
    private $image;

    /**
     * @var Promotion[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Promotion",mappedBy="products")
     */
    private $promotions;

    private $promoPrice;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Shipper",mappedBy="product")
     */
    private $cartProducts;

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }



    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }


    /**
     * @param User $user
     * @return Product
     */
    public function setOwner(User $user)
    {
        $this->owner = $user;
        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }


    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    public function getCategory()
    {
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
        $this->quantity = $quantity;
        return $this;
    }


    public function decreaseQuantity(int $quantity){

        $this->quantity -= $quantity;
        if($this->quantity <= 0){
            $this->setIsActive(0);
            $this->quantity = 0;
        }
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

    public function getSummary(){
        return substr($this->description,0,50) . '...';
    }

    /**
     * @return mixed
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param mixed $promotions
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    /**
     * @return mixed
     */
    public function getPromoPrice()
    {
        return $this->promoPrice;
    }

    /**
     * @return mixed
     */
    public function getCartProducts()
    {
        return $this->cartProducts;
    }

    /**
     * @param mixed $cartProducts
     */
    public function setCartProducts($cartProducts)
    {
        $this->cartProducts = $cartProducts;
    }



    /**
     * @param int $discount
     * @internal param mixed $promoPrice
     */
    public function setPromoPrice(int $discount){
        $basePrice = $this->getPrice();
        $promoPrice = $basePrice - ($basePrice / 100.0) * $discount;
        $this->promoPrice = $promoPrice;
    }

    public function getTopPromotion(User $user = null)
    {
        /** @var ArrayCollection $promotions */
        $promotions = $this->getPromotions();

        if (null != $user) {
            $promotions = $promotions->filter(function (Promotion $p) use ($user) {
                return ($p->getUsers()->contains($user));
            });
            $promotions = $promotions->filter(function (Promotion $p){
                $currDate = new \DateTime('NOW');
                return ($currDate >= $p->getStartDate() and $currDate <= $p->getEndDate() ? 1 : 0);
            });
        }  else {
            $promotions = $promotions->filter(function (Promotion $p){
                $currDate = new \DateTime('NOW');
                return ($currDate >= $p->getStartDate() and $currDate <= $p->getEndDate() and $p->getType() == 1 ? 1 :0);
            });
        }
        $iterator = $promotions->getIterator();
        $iterator->uasort(function (Promotion $a,Promotion $b) {
            return ($a->getDiscount() < $b->getDiscount()) ? 1 : -1;
        });
        $collection = new ArrayCollection(iterator_to_array($iterator));
        $promotion = $collection->first();
        if(is_object($promotion)) {
            $this->setPromoPrice($promotion->getDiscount());
            return $promotion;
        }
        return null;
    }

}

