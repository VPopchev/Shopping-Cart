<?php
namespace AppBundle\Service;


use AppBundle\Entity\Product;

class Paginator
{
    /**
     * @var \Generator|Product[]
     */
    private $products;

    /**
     * @var integer
     */
    private $currentPage;

    /**
     * @var integer
     */
    private $allPages;

    /**
     * @var boolean
     */
    private $hasPrevious;

    /**
     * @var boolean
     */
    private $hasNext;

    /**
     * Paginator constructor.
     * @param \Generator|Product[] $products
     * @param int $currentPage
     * @param int $allPages
     */
    public function __construct($currentPage,$allPages,$products)
    {
        $this->products = $products;
        $this->currentPage = $currentPage;
        $this->allPages = $allPages;
        $this->hasPrevious = $currentPage > 1;
        $this->hasNext = $currentPage < $allPages;
    }


    /**
     * @return \Generator
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param \Generator|
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getAllPages(): int
    {
        return $this->allPages;
    }

    /**
     * @param int $allPages
     */
    public function setAllPages(int $allPages)
    {
        $this->allPages = $allPages;
    }

    /**
     * @return bool
     */
    public function hasPrevious(): bool
    {
        return $this->hasPrevious;
    }

    /**
     * @param bool $hasPrevious
     */
    public function setPrevious(bool $hasPrevious)
    {
        $this->hasPrevious = $hasPrevious;
    }

    /**
     * @return bool
     */
    public function hasNext(): bool
    {
        return $this->hasNext;
    }

    /**
     * @param bool $hasNext
     */
    public function setNext(bool $hasNext)
    {
        $this->hasNext = $hasNext;
    }
}