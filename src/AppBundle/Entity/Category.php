<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
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
     * @Assert\NotBlank(message="Category name cannot be empty")
     * @Assert\Length(
     *     min="3",
     *     max="150",
     *     minMessage="Category name must be at least {{ limit }} characters long!",
     *     maxMessage="Category name cannot be more than {{ limit }} characters long!"
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="category")
     */
    private $products;


    /**
     * @var Category
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Category",mappedBy="parent")
     */
    private $children;

    /**
     * @var ArrayCollection|Category[]
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category",inversedBy="children")
     * @ORM\JoinColumn(name="parent_id",referencedColumnName="id",onDelete="CASCADE")
     */
    private $parent;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getProducts(){
        return $this->products;
    }

    public function getActiveProducts(){
        $criteria = Criteria::create()->where(Criteria::expr()
            ->eq("isActive",'1'));
        return $this->products->matching($criteria)->toArray();
    }


    /**
     * @param Product $product
     * @return Category
     */
    public function addProduct(Product $product){
        $this->products[] = $product;
        return $this;
    }

    /**
     * @return Category
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Category $children
     */
    public function setChildren(Category $children)
    {
        $this->children = $children;
    }

    /**
     * @return Category[]|ArrayCollection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Category[]|ArrayCollection $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
     * @return Category
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

    public function __toString()
    {
        return "".$this->id;
    }
}

